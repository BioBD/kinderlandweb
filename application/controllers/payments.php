<?php
    require_once APPPATH . 'core/CK_Controller.php';
    require_once APPPATH . 'core/cielotransaction.php';
    
    class Payments extends CK_Controller {

        public function __construct() {
            parent::__construct();
            $this -> load -> helper('url');
            $this -> load -> model('cielotransaction_model');
            $this -> load -> model('donation_model');
            $this -> load -> model('eventsubscription_model');
            $this -> load -> model('event_model');
            $this -> load -> model('person_model');
            $this -> load -> model('personuser_model');

            $this->cielotransaction_model->setLogger($this->Logger);
            $this->donation_model->setLogger($this->Logger);
            $this->eventsubscription_model->setLogger($this->Logger);
            $this->person_model->setLogger($this->Logger);
            $this->personuser_model->setLogger($this->Logger);
            $this->event_model->setLogger($this->Logger);
        }

        public function index() {
            $this -> Logger -> info("Starting " . __METHOD__);
            $transactionList = $this -> cielotransaction_model -> getAllPayments();
            $data['transactions'] = $transactionList;
            $this -> loadView("payments/home", $data);
        }

        public function info($tId) {
            $this -> Logger -> info("Starting " . __METHOD__);

            $transaction = $this -> cielotransaction_model -> getPaymentById($tId);
            $data['transaction'] = $transaction;
            
            $this -> loadView('payments/info', $data);
        }

        public function checkout($donationId) {
            $this -> Logger -> info("Starting " . __METHOD__);

            try{
                $donation = $this->donation_model->getDonationById($donationId);
                $portionsMax = $this->donation_model->getDonationPortionsMax($donation);

                $data['donation'] = $donation;
                $data['portions'] = $portionsMax;

                $this -> loadView("payments/checkout", $data);
            } catch (Exception $ex) {
                $data['error'] = true;
                $this -> loadView("payments/checkout", $data);
            }
            
        }

        public function executarPagamentoSimples() {
            $this -> Logger -> info("Starting " . __METHOD__);

            $donation_id = $this -> input -> post('donation_id', TRUE);
            $donation = $this->donation_model->getDonationById($donation_id);
            if ($donation->getDonationStatus() != 1){
                $this->Logger->info("Donation outdated or closed. Redirecting to screen reporting this problem");
                $data['donation'] = $donation;
                $this->loadView("payments/checkout_timeout", $data);
                return;
            }
            $transaction_value = $donation->getDonatedValue();//$this -> input -> post('transaction_value', TRUE);
            $description = $this -> input -> post('description', TRUE);
            $card_flag = $this -> input -> post('card_flag', TRUE);
            $payment_portions = $this -> input -> post('payment_portions', TRUE);
			if($payment_portions === FALSE)
				$payment_portions = '1';

            $transaction_value = preg_replace("/\D/", ".", $transaction_value);
            

            $transaction = CieloTransaction::createCieloTransaction($donation_id, $transaction_value, $card_flag, $payment_portions);
            $xml = $transaction -> createTransactionCieloBuyPage($description);

            $status = $xml->status;
            if ($status == NULL || ($status != "0" && $status != "10" && $status != "1" ) ) {
                $this -> Logger -> error("Erro ao iniciar operação com a Cielo, código de erro: " . $status . "\nDetalhes do erro: " . var_export($xml, true));
                //TODO Pagina de erro.                
                return;
            }
            
            $tid = $xml->tid;
            $url_redirect = $xml->{'url-autenticacao'};
            if ($tid == NULL || $url_redirect == NULL || strlen($tid) < 3 || strlen($url_redirect) < 3) {
                $this -> Logger -> error("Mensagem inválida recebida da operadora.");
                //TODO Pagina de erro.                
                return;
            }

            $transaction -> setTId($tid);

            $data["data"] = $this -> cielotransaction_model -> insertNewPayment($transaction);
            header("Location: ".$url_redirect);
            die();
        }

        public function retornoPagamento(){
            $this->Logger->info("Running: ". __METHOD__);
            $donation_id = $this -> input -> get('donation_id', TRUE);
            $this->Logger->info("Payment result from cielo for donation_id: ". $donation_id);
            
            $payments = $this->cielotransaction_model -> getAllPaymentsByDonationId($donation_id);
                 
            $retorno = array();
                        
            foreach($payments as $payment){
                $xml = $this->updatePaymentStatus($payment);                
                $retorno = $xml;                
            }
            $this->Logger->info("Transaction status returned by Cielo: ". $retorno->status);             
            $data["transactions"] = array(CieloTransaction::getTransactionStatusText($retorno->status));
            
            $this -> loadView("payments/result", $data);                        
        }
		
        public function rotinaPagamentos(){
            $this->Logger->info("Iniciando rotina de recuperacao de pagamentos Cielo");
        	$donations = $this->donation_model->getAllPendingTransactions();
            $retorno = array();
            
            $this->Logger->info("Doacoes a serem analisadas");
            $this->Logger->debug(print_r($donations, true));

            $abandoned = 0;

            foreach($donations as $donation){
                $this->Logger->info("Pagamentos:");
	            $payments = $this->cielotransaction_model -> getAllPaymentsByDonationId($donation->getDonationId());
                $this->Logger->debug(print_r($payments, true));
                if(count($payments) == 0) {
                    $this->Logger->info("Configurando doacao como abandonada");
                    $abandoned++;
                    $this->donation_model->updateDonationStatus($donation->getDonationId(), DONATION_STATUS_ABANDONED);
                } else {
                    foreach($payments as $payment){
                        $this->Logger->info("Atualizando status");
                        $xml = $this->updatePaymentStatus($payment);                
                        $retorno[] = $xml;   
                        $this->Logger->debug(print_r($xml, true));             
                    }
                }   
            }               
            $this->Logger->info("Total de doacoes processadas: ". count($donations));
            $this->Logger->info("Total de doacoes abandonadas: ". $abandoned);
            $this->Logger->info("Total de doacoes atualizadas: ". (count($donations)-$abandoned));

            $this->Logger->info("Fim da execucao da rotina de atualizacao de pagamentos");
        }
		

        public function updatePaymentStatus($payment){
                $xml = $payment->requestTransactionResult();
                
                $status = $xml->status;
                if ($status == NULL) {
                    $this -> Logger -> error("Erro ao iniciar operação com a Cielo, código de erro: " . $status . "\nDetalhes do erro: " . var_export($xml, true));
                    //TODO PAGINA DEERRO
                }
				$this->cielotransaction_model -> updatePaymentStatus($payment,$status);
                
                $donation = $this->donation_model->getDonationById($payment->getDonation_id());               
                if($status == CieloTransaction::TRANSACAO_CAPTURADA){
					if($donation->getDonationStatus() == DONATION_STATUS_PAID)
						return $xml;
                    $this->donation_model->updateDonationStatus($payment->getDonation_id(), DONATION_STATUS_PAID);
                    $this->eventsubscription_model->updateSubscriptionsStatusByDonationId($payment->getDonation_id(), SUBSCRIPTION_STATUS_SUBSCRIBED);                  
                    $this->summercamp_model->paidDonation($payment->getDonation_id());  
					$this->sendPaymentConfirmationMail($donation,$payment);			
					return $xml;
                } else if($status == CieloTransaction::TRANSACAO_CANCELADA){
                    if($donation->getDonationStatus() == DONATION_STATUS_PAID)
                        return $xml;
                    $this->donation_model->updateDonationStatus($payment->getDonation_id(), DONATION_STATUS_ABANDONED);
                    //$this->sendPaymentConfirmationMail($donation,$payment);
                    //$this->eventsubscription_model->updateSubscriptionsStatusByDonationId($payment->getDonation_id(), SUBSCRIPTION_STATUS_SUBSCRIBED);                  
                    return $xml;
                } else if($status == CieloTransaction::TRANSACAO_NAO_AUTORIZADA || $status == CieloTransaction::TRANSACAO_NAO_AUTENTICADA){
                    if($donation->getDonationStatus() == DONATION_STATUS_PAID)
                        return $xml;
                    $this->donation_model->updateDonationStatus($payment->getDonation_id(), DONATION_STATUS_ERROR);
                    //$this->sendPaymentConfirmationMail($donation,$payment);
                    //$this->eventsubscription_model->updateSubscriptionsStatusByDonationId($payment->getDonation_id(), SUBSCRIPTION_STATUS_SUBSCRIBED);                  
                    return $xml;
                }

                return $xml;
        }
        
        public function transactionResultToText($xml,$payment){
                $write = "TId de numero ".$payment->getTId()." tem agora o status: ".$this->cielotransaction_model->getPaymentById($payment->getTId())->getPayment_status();                
                switch($xml->status){
                    case CieloTransaction::TRANSACAO_CANCELADA:
                        $write .= " e tem como dados extras ".$xml->cancelamentos->cancelamento->mensagem;                    
                        break;
                    case CieloTransaction::TRANSACAO_CAPTURADA:
                        $write .= " e tem como dados extras ".$xml->captura->mensagem;
                        break;            
                }
                return $write;
        }

        public function history() {
            $this->Logger->info("Running: ". __METHOD__);
            $userid = $this->session->userdata("user_id");
            if(isset($_GET['userid'])){
                $userid = $_GET['userid'];
                $donations = $this->donation_model->getDonationsByUserId($userid);
				foreach($donations as $donation){
					$summerCampSubscriptions = $this->summercamp_model->getSubscriptionsByDonation($donation->donation_id);
				
					if($summerCampSubscriptions){
						$donation->extra = array();
						foreach($summerCampSubscriptions as $summerCampSubscription){
							$campName = $this -> summercamp_model -> getSummerCampById($summerCampSubscription -> getSummerCampId()) -> getCampName();
							$donation->extra[] = "<br>Colonista: ".$summerCampSubscription->getFullname()."<br>Colonia: ".$campName;
						}
					}
					
					if($donation->donation_type == 'inscrição'){
						$donation->extra = array();
						$event = $this->event_model->getDonationEvent($donation->donation_id);
						$donation->extra[] = "em evento ".$event->getEventName();
					}
				}
                $data['donations'] = $donations;
                $data['user'] = $this->person_model->getPersonById($userid);
                $this->loadReportView("reports/finances/user_donation_history", $data);
            } else {
                $donations = $this->donation_model->getDonationsByUserId($userid);
				foreach($donations as $donation){
					$summerCampSubscriptions = $this->summercamp_model->getSubscriptionsByDonation($donation->donation_id);
					if($summerCampSubscriptions){
						$donation->extra = array();
						foreach($summerCampSubscriptions as $summerCampSubscription){
							$campName = $this -> summercamp_model -> getSummerCampById($summerCampSubscription -> getSummerCampId()) -> getCampName();
							$donation->extra[] = "Colonista: ".$summerCampSubscription->getFullname()."<br>Colonia: ".$campName."<br>";
						}
					}			
					
					if($donation->donation_type == 'inscrição'){
						$donation->extra = array();
						$event = $this->event_model->getDonationEvent($donation->donation_id);
						$donation->extra[] = "em evento ".$event->getEventName();
					}
				}
				
                $data['donations'] = $donations;

                $this->loadView("payments/history", $data);
            }

            
        }

    }
?>