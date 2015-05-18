<?php

include_once APPPATH . 'core/personuser.php';
include_once APPPATH . 'core/event.php';
include_once APPPATH . 'core/donation.php';
include_once APPPATH . 'libraries/logger.php';

// Colonia Kinderland Controller -> CK_Controller
class CK_Controller extends CI_Controller {


	protected $pid;
	protected $Logger;

	public function __construct() {
		parent::__construct();
		$this -> pid = getmypid();
		$this -> setLogger();
	}

	public function __destruct() {
		if ($this -> Logger)
			$this -> Logger -> endTransaction();
	}

	public function sendSignupEmail($person) {
		$this -> Logger -> info("Running :" . __METHOD__);

		$emailString = "Olá " . $person -> getFullname() . ", <br><br>" . "Seu cadastro foi efetuado com sucesso.<br><br><br><br>" . "Diretoria da Associação Kinderland";
		$emailSubject = "[Kinderland] Confirmação de cadastro";

		return $this -> sendMail($emailSubject, $emailString, $person);
	}

	public function sendPaymentConfirmationMail($donation, $payment) {
		if ($donation -> getDonationType() == DONATION_TYPE_ASSOCIATE) {
			$person = $this -> person_model -> getPersonById($donation -> getPersonId());
			$emailString = "Prezad" . (($person -> getGender() == 'F') ? 'a' : 'o') . " " . $person -> getFullname() . ", <br><br>" . 
			"Sua doação para a Associação Kinderland foi recebida com sucesso. Estamos registrando seu
			CPF em nossa base de associados do ano 2015.<br><br>" . 
			"Esta contribuição é muito importante para nós, pois permite que façamos diversos investimentos 
			no patrimônio da Colônia para que as futuras gerações possam usufruir desta experiência única 
			que é a Kinderland.<br><br>
			Da mesma forma, sua colaboração permite que continuemos a promover os projetos sociais em 
			parceria com outras instituições, com o objetivo de contribuir para a construção de uma 
			sociedade mais justa e igualitária.<br><br>
			Os sócios-doadores da Kinderland têm, entre outros, os seguintes benefícios:<br><br>" .
			"<ul>" .
			  "<li>" . "Desconto nas doações pela cessão do espaço físico da colônia Kinderland para festas de aniversário, finais de semana com amigos ou outros eventos particulares;</li>" .
			  "<li>" . "Desconto nos eventos especiais organizados e realizados pela Associação Kinderland (ex. evento MaCK - Mostre a Colônia Kinderland);</li>" .
			  "<li>" . "Pré-inscrição antecipada <b>sem garantia de vaga</b> para a temporada de verão</li>" .
			"</ul>" .
			"<br><br>" .
			"Recomendamos que leiam atentamente as condições dos sócios-doadores  disponíveis no nosso site: 
			<a href='http://www.kinderland.com.br/como-ajudar/quero-ser-socio/' target='_blank'>http://www.kinderland.com.br/como-ajudar/quero-ser-socio/</a> <br><br>
 			Mais uma vez, nosso MUITO OBRIGADO!<br><br>Diretoria da Associação Kinderland";
			$emailSubject = "[Kinderland] Doacao campanha associados";

			return $this -> sendMail($emailSubject, $emailString, $person,array("secretaria@kinderland.com.br"));
		} else if ($donation -> getDonationType() == DONATION_TYPE_FREEDONATION) {
			$person = $this -> person_model -> getPersonById($donation -> getPersonId());
			$emailString = "Prezad" . (($person -> getGender() == 'F') ? 'a' : 'o') . " " . $person -> getFullname() . ", <br><br>" . "Sua doação para a Kinderland
			foi recebida com sucesso. <br><br>" . 
			"A Associação KINDERLAND é uma entidade sem fins lucrativos que necessita de <br>" .
			"contribuições e doações regulares. Elas são utilizadas na manutenção do espaço onde <br>" .
			"a Colônia de Férias é realizada, além de ajudar com os custos operacionais durante o ano <br><br>" .
			"Agradecemos a todos que indistintamente contribuem como associados ou doadores.<br>" .
			"A Associação Kinderland realiza projetos sociais com jovens de comunidades carentes,<br>" .
			"oferece bolsas parciais ou integrais para crianças do Lar da Criança nas temporadas <br>" .
			"de verão e participa de várias outras iniciativas comunitárias. Somente com estas <br>" .
			"contribuições regulares isto torna-se possível. <br><br>" .
			"Muito obrigado pela sua contribuição, ela é muito importante para
			nós.<br><br><br><br>" . "Diretoria da Associação Kinderland";
			$emailSubject = "[Kinderland] Doacao avulsa";

			return $this -> sendMail($emailSubject, $emailString, $person,array("secretaria@kinderland.com.br"));
		} else if ($donation -> getDonationType() == DONATION_TYPE_SUBSCRIPTION) {
			$person = $this -> person_model -> getPersonById($donation -> getPersonId());
			$event = $this -> event_model -> getDonationEvent($donation -> getDonationId());
			$emailString = "Prezad" . (($person -> getGender() == 'F') ? 'a' : 'o') . " " . $person -> getFullname() . ", <br><br>" . "Sua inscrição para o " . $event -> getEventName() . " foi recebida com sucesso. <br><br>" . "Muito obrigado pela sua contribuição, ela é muito importante para
			nós.<br><br><br><br>" . "Diretoria da Associação Kinderland";
			$emailSubject = "[Kinderland] Inscricao " . $event -> getEventName() . " confirmada";

			return $this -> sendMail($emailSubject, $emailString, $person,array("secretaria@kinderland.com.br"));
		}
	}

    public function sendNewPasswordEmail($person, $randomString) {
        $emailSubject = "[Kinderland] Nova senha";
        $emailString = "Prezad" . (($person->getGender() == 'F') ? 'a' : 'o') . " " . $person->getFullname() . ", <br><br>" . "Sua nova senha é: " .
                $randomString . "<br><br>" . "Por favor, lembre-se de alterar essa senha para uma senha de sua preferência quando entrar no sistema.". 
                "<br><br>". "Caso você não tenha solicitado essa mudança de senha, favor entrar em contato com a secretaria Kinderland.". 
                "<br><br><br><br>" . "Diretoria da Associação Kinderland";

        return $this->sendMail($emailSubject, $emailString, $person,array("secretaria@kinderland.com.br"));
    }

	protected function sendMail($subject, $content, $person, $cc = NULL, $bcc = NULL) {
//		$myMail = "testekinderland2015@gmail.com";
//		$config = Array('protocol' => 'smtp', 'smtp_host' => 'ssl://smtp.gmail.com', 'smtp_port' => 465, 'smtp_user' => $myMail, 'smtp_pass' => 'testandoteste', 'mailtype' => 'html', 'charset' => mb_internal_encoding(), 'wordwrap' => TRUE);

		$myMail = "secretaria@kinderland.com.br";
		$config = Array('protocol' => 'smtp', 'smtp_host' => 'ssl://br154.hostgator.com.br', 'smtp_port' => 465, 'smtp_user' => $myMail, 'smtp_pass' => 'Kinder155', 'mailtype' => 'html', 'charset' => mb_internal_encoding(), 'wordwrap' => TRUE);

		$this -> load -> library('email', $config);
		$this->load->model('email_model');
		$this->email_model->setLogger($this->Logger);

		$this -> email -> from($myMail);
		$this -> email -> to($person -> getEmail());
		$this -> email -> set_newline("\r\n");
		$this -> email -> subject($subject);
		$this -> email -> message($content);
		if($cc == NULL){
			$cc = array("secretaria@kinderland.com.br");
		}
		if ($cc != NULL)
			$this -> email -> cc($cc);
		if ($bcc != NULL)
			$this -> email -> bcc($bcc);
		if ($this -> email -> send()) {
			$this -> email_model-> saveEmail($subject, $content, $person -> getEmail(), $cc, $bcc, TRUE);
			$this -> Logger -> info("Email enviado com sucesso para: " . $person -> getFullname() . " com o assunto " . $subject);
			return TRUE;
		} else {
			$this -> email_model-> saveEmail($subject, $content, $person -> getEmail(), $cc, $bcc, TRUE);
			$this -> Logger -> error("Problema ao enviar email para: " . $person -> getFullname() . " com o assunto " . $subject . "\n Texto de debug foi: " . $this -> email -> print_debugger());
			return FALSE;
		}
	}

	private function setLogger() {
		$this -> config -> load('logger', true);
		$logPath = $this -> config -> item('log_path', 'logger');
		//$logFilename = strtolower(get_class($this));
		$logLevel = $this -> config -> item('log_level', 'logger');
		//$this->Logger = new Logger($logLevel, $logPath, $logFilename);
		$this -> Logger = new Logger($logLevel, $logPath, "kinderland");
		$this -> Logger -> startTransaction();
		$this -> Logger -> info('[ENVIRONMENT][' . strtoupper(ENVIRONMENT) . ']');
		$this -> Logger -> info("[PROCESS ID][{$this->pid}]");
	}

	public function loadView($viewName, $data = array()) {
		if ($this -> session -> userdata("fullname")) {
			$data['fullname'] = $this -> session -> userdata("fullname");
			$data['user_id'] = $this -> session -> userdata("user_id");
			$data['gender'] = $this -> session -> userdata("gender");
			$data['permissions'] = $this -> session -> userdata("user_types");
		}

		$output = $this -> load -> view('include/header', $data, true);
		$output .= $this -> load -> view($viewName, $data, true);
		$output .= $this -> load -> view('include/footer', $data, true);
		$this -> output -> set_output($output);
	}

	public function loadReportView($viewName, $data = array()) {
		$output = $this -> load -> view('include/report/header', true);
		$output .= $this -> load -> view($viewName, $data, true);
		$this -> output -> set_output($output);
	}

	public static function toMMDDYYYY($date){
		if($date !== "" && $date !== FALSE && $date !== NULL){
			$date = explode("/", $date);
			return $date[1]."/".$date[0]."/".$date[2];
		}
	}
	
	public static function verifyAntecedence($dateBefore,$dateAfter){
		if($dateBefore == NULL || $dateAfter == NULL)
			return FALSE;
		$dateBefore = implode('', array_reverse(explode('/', $dateBefore)));
		$dateAfter = implode('', array_reverse(explode('/', $dateAfter)));
		return $dateBefore <= $dateAfter;
	}

	public function checkSession() {
		$this -> Logger -> info("Running: " . __METHOD__);
		if (!$this -> session -> userdata('user_id') || !$this -> session -> userdata('fullname')) {
			$this -> Logger -> info("Session expired or doesnt exist");
			return false;
		}

		$this -> Logger -> info("Session ok - User " . $this -> session -> userdata('user_id'));
		return true;
	}

	public function denyAcess($methodName) {
		$this -> Logger -> warn("Usuário com id =" . $this -> session -> userdata("user_id") . " tentou se conectar ao methodo " . $methodName . " que ele não possui acesso.");
		return redirect("login/index");
	}

	public function checkPermition($permitions = array()) {

		foreach ($permitions as $permition) {
			foreach ($this->session->userdata('user_types') as $permitionUser) {
				if ($permition == $permitionUser)
					return true;
			}
		}
		return false;
	}

}
?>