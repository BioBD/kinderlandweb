<?php

require_once APPPATH . 'core/CK_Controller.php';
require_once APPPATH . 'core/summercamp.php';
require_once APPPATH . 'core/summercampSubscription.php';
require_once APPPATH . 'core/colonist.php';
require_once APPPATH . 'core/event.php';
require_once APPPATH . 'controllers/events.php';

class Admin extends CK_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('person_model');
        $this->load->model('personuser_model');
        $this->load->model('summercamp_model');
        $this->load->model('colonist_model');
        $this->load->model('address_model');
        $this->load->model('telephone_model');
        $this->load->model('donation_model');
        $this->load->model('generic_model');
        $this->load->model('validation_model');
        $this->load->model('email_model');
        $this->load->model('event_model');
        $this->load->model('eventsubscription_model');
        $this->load->model('campaign_model');
        $this->person_model->setLogger($this->Logger);
        $this->personuser_model->setLogger($this->Logger);
        $this->summercamp_model->setLogger($this->Logger);
        $this->colonist_model->setLogger($this->Logger);
        $this->address_model->setLogger($this->Logger);
        $this->telephone_model->setLogger($this->Logger);
        $this->donation_model->setLogger($this->Logger);
        $this->generic_model->setLogger($this->Logger);
        $this->validation_model->setLogger($this->Logger);
        $this->email_model->setLogger($this->Logger);
        $this->event_model->setLogger($this->Logger);
        $this->eventsubscription_model->setLogger($this->Logger);
        $this->campaign_model->setLogger($this->Logger);
    }

    public function campaign_admin() {
        $this->loadView("admin/campaigns/campaign_admin_container");
    }

    public function associated_campaign() {
        $this->loadView("admin/campaigns/associated_campaign");
    }

    public function event_admin() {
        $this->loadView("admin/events/event_admin_container");
    }

    public function completeEvent() {
        $this->Logger->info("Starting " . __METHOD__);

        $event_name = $this->input->post('event_name', TRUE);
        $description = $this->input->post('description', TRUE);
        $date_start = $this->input->post('date_start', TRUE);
        $date_finish = $this->input->post('date_finish', TRUE);
        $date_start_show = $this->input->post('date_start_show', TRUE);
        $date_finish_show = $this->input->post('date_finish_show', TRUE);
        $capacity_male = $this->input->post('capacity_male', TRUE);
        $capacity_female = $this->input->post('capacity_female', TRUE);
        $capacity_nonsleeper = $this->input->post('capacity_nonsleeper', TRUE);
        $payments = array();
        $payment_date_end = $this->input->post("payment_date_end", TRUE);
        $payment_date_start = $this->input->post("payment_date_start", TRUE);
        $full_price = $this->input->post("full_price", TRUE);
        $children_price = $this->input->post("children_price", TRUE);
        $middle_price = $this->input->post("middle_price", TRUE);
        $payment_portions = $this->input->post("payment_portions", TRUE);
        $associated_discount = $this->input->post("associated_discount", TRUE);
        $enabled = $this->input->post("enabled", TRUE);
        $errors = array();


        if ($event_name === "")
            $errors[] = "O campo nome é obrigatório";
        if (!$date_start)
            $date_start = NULL;
        if (!$date_start_show)
            $date_start_show = NULL;
        if (!$date_finish)
            $date_finish = NULL;
        if (!$date_finish_show)
            $date_finish_show = NULL;
        if (!$enabled)
            $enabled = "false";
        else if ($enabled === "1")
            $enabled = "true";

        if ($date_start && $date_finish && !Events::verifyAntecedence($date_start, $date_finish))
            $errors[] = "A data do ínicio do período do evento antecede a data de fim do evento";

        if ($date_start_show && $date_finish_show && !Events::verifyAntecedence($date_start_show, $date_finish_show))
            $errors[] = "A data do ínicio do período de inscrições antecede a data de fim do periodo de inscrições";

        if ($date_start && $date_finish_show && Events::verifyAntecedence($date_start, $date_finish_show))
            $errors[] = "A data do ínicio do período do evento antecede a data de fim de inscrições";

        if ($capacity_male === "")
            $capacity_male = 0;
        if ($capacity_female === "")
            $capacity_female = 0;
        if ($capacity_nonsleeper === "")
            $capacity_nonsleeper = 0;

        if (is_array($full_price)) {
            for ($i = 0; $i < count($full_price); $i++) {
                if (!$payment_date_start[$i])
                    $errors[] = "O periodo de pagamento de numero " . ($i + 1) . " não tem data de inicio";
                if (!$payment_date_end[$i])
                    $errors[] = "O periodo de pagamento de numero " . ($i + 1) . " não tem data de fim";
                if (!$full_price[$i])
                    $errors[] = "O periodo de pagamento de numero " . ($i + 1) . " não tem valor ";
                if (!$middle_price[$i])
                    $middle_price[$i] = $full_price[$i];
                if (!$children_price[$i])
                    $children_price[$i] = $middle_price[$i];
                if ($payment_date_start[$i] && $payment_date_end[$i] && !Events::verifyAntecedence($payment_date_start[$i], $payment_date_end[$i])) {
                    $errors[] = "O pagamento de numero " . ($i + 1) . " tinha data de fim anterior a data de inicio";
                }
                if ($payment_date_start[$i] && $payment_date_end[$i] && (
                        !Events::verifyAntecedence($payment_date_start[$i], $date_finish_show) ||
                        !Events::verifyAntecedence($payment_date_end[$i], $date_finish_show) ||
                        !Events::verifyAntecedence($date_start_show, $payment_date_start[$i]) )
                )
                    $errors[] = "O pagamento de numero " . ($i + 1) . " está fora do periodo de inscrições";
                for ($j = $i + 1; $j < count($full_price); $j++) {
                    if
                    (
                            (
                            Events::verifyAntecedence($payment_date_start[$i], $payment_date_start[$j]) && Events::verifyAntecedence($payment_date_start[$j], $payment_date_end[$i])
                            ) ||
                            (
                            Events::verifyAntecedence($payment_date_start[$j], $payment_date_start[$i]) && Events::verifyAntecedence($payment_date_start[$i], $payment_date_end[$j])
                            )
                    )
                        $errors[] = "Os pagamentos de numero" . ($i + 1) . " e " . ($j + 1) . " se sobrepoem";
                }

                $payment_date_start[$i] = explode("/", $payment_date_start[$i]);
                $payment_date_start[$i] = strval($payment_date_start[$i][2]) . "-" . strval($payment_date_start[$i][1]) . "-" . strval($payment_date_start[$i][0]);

                $payment_date_end[$i] = explode("/", $payment_date_end[$i]);
                $payment_date_end[$i] = strval($payment_date_end[$i][2]) . "-" . strval($payment_date_end[$i][1]) . "-" . strval($payment_date_end[$i][0] . " 23:59:59");

                $date_start = explode("/", $date_start);
                $date_start = strval($date_start[2]) . "-" . strval($date_start[1]) . "-" . strval($date_start[0]);

                $date_finish = explode("/", $date_finish);
                $date_finish = strval($date_finish[2]) . "-" . strval($date_finish[1]) . "-" . strval($date_finish[0] . " 23:59:59");

                $date_start_show = explode("/", $date_start_show);
                $date_start_show = strval($date_start_show[2]) . "-" . strval($date_start_show[1]) . "-" . strval($date_start_show[0]);

                $date_finish_show = explode("/", $date_finish_show);
                $date_finish_show = strval($date_finish_show[2]) . "-" . strval($date_finish_show[1]) . "-" . strval($date_finish_show[0] . " 23:59:59");

                $payments[] = array(
                    "payment_date_start" => $payment_date_start[$i],
                    "payment_date_end" => $payment_date_end[$i],
                    "full_price" => $full_price[$i],
                    "children_price" => $children_price[$i],
                    "middle_price" => $middle_price[$i],
                    "payment_portions" => $payment_portions[$i],
                    "associated_discount" => $associated_discount[$i] / 100,
                );
            }
        } else if ($full_price !== FALSE) {
            if (!$payment_date_start)
                $errors[] = "O pagamento não tem data de inicio";
            if (!$payment_date_end)
                $errors[] = "O pagamento não tem data de fim";
            if (!$full_price)
                $errors[] = "O pagamento não tem valor ";
            if (!$middle_price)
                $middle_price = $full_price;
            if (!$children_price)
                $children_price = $middle_price;
            if ($payment_date_start[$i] && $payment_date_end[$i] && !Events::verifyAntecedence($payment_date_start[$i], $payment_date_end[$i])) {
                $errors[] = "O pagamento de numero " . ($i + 1) . " tinha data de fim anterior a data de inicio";
            }

            for ($i = 0; $i < count($full_price); $i++) {

                $payment_date_start[$i] = explode("/", $payment_date_start[$i]);
                $payment_date_start[$i] = strval($payment_date_start[$i][2]) . "-" . strval($payment_date_start[$i][1]) . "-" . strval($payment_date_start[$i][0]);

                $payment_date_end[$i] = explode("/", $payment_date_end[$i]);
                $payment_date_end[$i] = strval($payment_date_end[$i][2]) . "-" . strval($payment_date_end[$i][1]) . "-" . strval($payment_date_end[$i][0] . " 23:59:59");
            }

            $payments[] = array(
                "payment_date_start" => $payment_date_start,
                "payment_date_end" => $payment_date_end,
                "full_price" => $full_price,
                "children_price" => $children_price,
                "middle_price" => $middle_price,
                "payment_portions" => $payment_portions,
                "associated_discount" => $associated_discount / 100,
            );

            $date_start = explode("/", $date_start);
            $date_start = strval($date_start[2]) . "-" . strval($date_start[1]) . "-" . strval($date_start[0]);

            $date_finish = explode("/", $date_finish);
            $date_finish = strval($date_finish[2]) . "-" . strval($date_finish[1]) . "-" . strval($date_finish[0] . " 23:59:59");

            $date_start_show = explode("/", $date_start_show);
            $date_start_show = strval($date_start_show[2]) . "-" . strval($date_start_show[1]) . "-" . strval($date_start_show[0]);

            $date_finish_show = explode("/", $date_finish_show);
            $date_finish_show = strval($date_finish_show[2]) . "-" . strval($date_finish_show[1]) . "-" . strval($date_finish_show[0] . " 23:59:59");
        }

        if (count($errors) > 0)
            return $this->eventCreate($errors, $event_name, $description, $date_start, $date_finish, $date_start_show, $date_finish_show, $capacity_male, $capacity_female, $capacity_nonsleeper, $payments);


        try {
            $this->Logger->info("Inserting new event");
            $this->generic_model->startTransaction();

            $eventId = $this->event_model->insertNewEvent($event_name, $description, $date_start, $date_finish, $date_start_show, $date_finish_show, $enabled, $capacity_male, $capacity_female, $capacity_nonsleeper);

            if ($eventId) {
                foreach ($payments as $payment) {
                    $this->event_model->insertNewPaymentPeriod($eventId, $payment["payment_date_start"], $payment["payment_date_end"], $payment["full_price"], $payment["middle_price"], $payment["children_price"], $payment["associated_discount"], $payment["payment_portions"]);
                }

                $this->generic_model->commitTransaction();
                $this->Logger->info("New event successfully inserted");
                echo "<script>alert('Evento criado com sucesso!');opener.location.reload(); window.close();</script>";
                //redirect("events/manageEvents");
            } else
                echo "<script>alert('Ocorreu um erro ao criar o evento. Tente novamente.');window.history.back();</script>";
        } catch (Exception $ex) {
            $this->Logger->error("Failed to insert new event");
            $this->generic_model->rollbackTransaction();
            $data['error'] = true;

            $this->loadReportView('admin/events/event_create', $data);
        }
    }

    public function eventCreate($errors = array(), $event_name = NULL, $description = NULL, $date_start = NULL, $date_finish = NULL, $date_start_show = NULL, $date_finish_show = NULL, $capacity_male = NULL, $capacity_female = NULL, $capacity_nonsleeper = NULL, $payments = array()) {
        $this->Logger->info("Starting " . __METHOD__);
        $data = array();
        $data["errors"] = $errors;
        $data["event_name"] = $event_name;
        $data["description"] = $description;
        $data["date_start"] = Events::toMMDDYYYY($date_start);
        $data["date_finish"] = Events::toMMDDYYYY($date_finish);
        $data["date_start_show"] = Events::toMMDDYYYY($date_start_show);
        $data["date_finish_show"] = Events::toMMDDYYYY($date_finish_show);
        $data["capacity_male"] = $capacity_male;
        $data["capacity_female"] = $capacity_female;
        $data["capacity_nonsleeper"] = $capacity_nonsleeper;
        $data["payments"] = $payments;

        $this->loadView('admin/events/event_create', $data);
    }

    public function editEvent($event_id = NULL, $errors = array(), $event_name = NULL, $description = NULL, $date_start = NULL, $date_finish = NULL, $date_start_show = NULL, $date_finish_show = NULL, $capacity_male = NULL, $capacity_female = NULL, $capacity_nonsleeper = NULL, $payments = array()) {
        $eventId = $event_id;

        $event = $this->event_model->getEventById($eventId);
        $paymentPeriods = $this->event_model->getEventPaymentPeriods($eventId);

        $data['event_id'] = $eventId;
        $data['event_name'] = $event->getEventName();
        $data['description'] = $event->getDescription();
        $data["errors"] = $errors;

        $date = explode("-", $event->getDateStart());
        $dateDay = explode(" ", $date[2]);
        $date = $dateDay[0] . "/" . $date[1] . "/" . $date[0];
        $data['date_start'] = $date;

        $date = explode("-", $event->getDateFinish());
        $dateDay = explode(" ", $date[2]);
        $date = $dateDay[0] . "/" . $date[1] . "/" . $date[0];
        $data['date_finish'] = $date;

        $date = explode("-", $event->getDateStartShow());
        $dateDay = explode(" ", $date[2]);
        $date = $dateDay[0] . "/" . $date[1] . "/" . $date[0];
        $data['date_start_show'] = $date;

        $date = explode("-", $event->getDateFinishShow());
        $dateDay = explode(" ", $date[2]);
        $date = $dateDay[0] . "/" . $date[1] . "/" . $date[0];
        $data['date_finish_show'] = $date;

        $data['enabled'] = $event->isEnabled();
        $data['capacity_male'] = $event->getCapacityMale();
        $data['capacity_female'] = $event->getCapacityFemale();
        $data['capacity_nonsleeper'] = $event->getCapacityNonSleeper();

        if ($payments == null) {
            foreach ($paymentPeriods as $payment) {
                $datePayment = explode("-", $payment->getDateStart());
                $dateDay = explode(" ", $datePayment[2]);
                $datePayment = $dateDay[0] . "/" . $datePayment[1] . "/" . $datePayment[0];
                $datePaymentEnd = explode("-", $payment->getDateFinish());
                $dateDay = explode(" ", $datePaymentEnd[2]);
                $datePaymentEnd = $dateDay[0] . "/" . $datePaymentEnd[1] . "/" . $datePaymentEnd[0];

                $payments[] = array(
                    "payment_date_start" => $datePayment,
                    "payment_date_end" => $datePaymentEnd,
                    "full_price" => $payment->getFullPrice(),
                    "children_price" => $payment->getChildrenPrice(),
                    "middle_price" => $payment->getMiddlePrice(),
                    "payment_portions" => $payment->getPortions(),
                    "associated_discount" => $payment->getAssociateDiscount(),
                );
            }
        }

        $data['payments'] = $payments;

        $this->loadView('admin/events/event_edit', $data);
    }

    public function updateEvent($event_id) {

        $this->Logger->info("Starting " . __METHOD__);

        $event_name = $this->input->post('event_name', TRUE);
        $description = $this->input->post('description', TRUE);
        $date_start = $this->input->post('date_start', TRUE);
        $date_finish = $this->input->post('date_finish', TRUE);
        $date_start_show = $this->input->post('date_start_show', TRUE);
        $date_finish_show = $this->input->post('date_finish_show', TRUE);
        $capacity_male = $this->input->post('capacity_male', TRUE);
        $capacity_female = $this->input->post('capacity_female', TRUE);
        $capacity_nonsleeper = $this->input->post('capacity_nonsleeper', TRUE);
        $payments = array();
        $payment_date_end = $this->input->post("payment_date_end", TRUE);
        $payment_date_start = $this->input->post("payment_date_start", TRUE);
        $full_price = $this->input->post("full_price", TRUE);
        $children_price = $this->input->post("children_price", TRUE);
        $middle_price = $this->input->post("middle_price", TRUE);
        $payment_portions = $this->input->post("payment_portions", TRUE);
        $associated_discount = $this->input->post("associated_discount", TRUE);
        $enabled = $this->input->post("enabled", TRUE);
        $errors = array();


        if ($event_name === "")
            $errors[] = "O campo nome é obrigatório";
        if (!$date_start)
            $date_start = NULL;
        if (!$date_start_show)
            $date_start_show = NULL;
        if (!$date_finish)
            $date_finish = NULL;
        if (!$date_finish_show)
            $date_finish_show = NULL;
        if (!$enabled)
            $enabled = "false";
        else if ($enabled === "1")
            $enabled = "true";

        if ($date_start && $date_finish && !Events::verifyAntecedence($date_start, $date_finish))
            $errors[] = "A data do ínicio do período do evento antecede a data de fim do evento";

        if ($date_start_show && $date_finish_show && !Events::verifyAntecedence($date_start_show, $date_finish_show))
            $errors[] = "A data do ínicio do período de inscrições antecede a data de fim do periodo de inscrições";

        if ($date_start && $date_finish_show && Events::verifyAntecedence($date_start, $date_finish_show))
            $errors[] = "A data do ínicio do período do evento antecede a data de fim de inscrições";

        if ($capacity_male === "")
            $capacity_male = 0;
        if ($capacity_female === "")
            $capacity_female = 0;
        if ($capacity_nonsleeper === "")
            $capacity_nonsleeper = 0;

        if (is_array($full_price)) {
            for ($i = 0; $i < count($full_price); $i++) {
                if (!$payment_date_start[$i])
                    $errors[] = "O periodo de pagamento de numero " . ($i + 1) . " não tem data de inicio";
                if (!$payment_date_end[$i])
                    $errors[] = "O periodo de pagamento de numero " . ($i + 1) . " não tem data de fim";
                if (!$full_price[$i])
                    $errors[] = "O periodo de pagamento de numero " . ($i + 1) . " não tem valor ";
                if (!$middle_price[$i])
                    $middle_price[$i] = $full_price[$i];
                if (!$children_price[$i])
                    $children_price[$i] = $middle_price[$i];
                if ($payment_date_start[$i] && $payment_date_end[$i] && !Events::verifyAntecedence($payment_date_start[$i], $payment_date_end[$i])) {
                    $errors[] = "O pagamento de numero " . ($i + 1) . " tinha data de fim anterior a data de inicio";
                }
                if ($payment_date_start[$i] && $payment_date_end[$i] && (
                        !Events::verifyAntecedence($payment_date_start[$i], $date_finish_show) ||
                        !Events::verifyAntecedence($payment_date_end[$i], $date_finish_show) ||
                        !Events::verifyAntecedence($date_start_show, $payment_date_start[$i]) )
                )
                    $errors[] = "O pagamento de numero " . ($i + 1) . " está fora do periodo de inscrições";
                for ($j = $i + 1; $j < count($full_price); $j++) {
                    if
                    (
                            (
                            Events::verifyAntecedence($payment_date_start[$i], $payment_date_start[$j]) && Events::verifyAntecedence($payment_date_start[$j], $payment_date_end[$i])
                            ) ||
                            (
                            Events::verifyAntecedence($payment_date_start[$j], $payment_date_start[$i]) && Events::verifyAntecedence($payment_date_start[$i], $payment_date_end[$j])
                            )
                    )
                        $errors[] = "Os pagamentos de numero" . ($i + 1) . " e " . ($j + 1) . " se sobrepoem";
                }

                $payment_date_start[$i] = explode("/", $payment_date_start[$i]);
                $payment_date_start[$i] = strval($payment_date_start[$i][2]) . "-" . strval($payment_date_start[$i][1]) . "-" . strval($payment_date_start[$i][0]);

                $payment_date_end[$i] = explode("/", $payment_date_end[$i]);
                $payment_date_end[$i] = strval($payment_date_end[$i][2]) . "-" . strval($payment_date_end[$i][1]) . "-" . strval($payment_date_end[$i][0] . " 23:59:59");

                $payments[] = array(
                    "payment_date_start" => $payment_date_start[$i],
                    "payment_date_end" => $payment_date_end[$i],
                    "full_price" => $full_price[$i],
                    "children_price" => $children_price[$i],
                    "middle_price" => $middle_price[$i],
                    "payment_portions" => $payment_portions[$i],
                    "associated_discount" => $associated_discount[$i] / 100,
                );
            }
        } else if ($full_price !== FALSE) {
            if (!$payment_date_start)
                $errors[] = "O pagamento não tem data de inicio";
            if (!$payment_date_end)
                $errors[] = "O pagamento não tem data de fim";
            if (!$full_price)
                $errors[] = "O pagamento não tem valor ";
            if (!$middle_price)
                $middle_price = $full_price;
            if (!$children_price)
                $children_price = $middle_price;
            if ($payment_date_start[$i] && $payment_date_end[$i] && !Events::verifyAntecedence($payment_date_start[$i], $payment_date_end[$i])) {
                $errors[] = "O pagamento de numero " . ($i + 1) . " tinha data de fim anterior a data de inicio";
            }

            for ($i = 0; $i < count($full_price); $i++) {

                $payment_date_start[$i] = explode("/", $payment_date_start[$i]);
                $payment_date_start[$i] = strval($payment_date_start[$i][2]) . "-" . strval($payment_date_start[$i][1]) . "-" . strval($payment_date_start[$i][0]);

                $payment_date_end[$i] = explode("/", $payment_date_end[$i]);
                $payment_date_end[$i] = strval($payment_date_end[$i][2]) . "-" . strval($payment_date_end[$i][1]) . "-" . strval($payment_date_end[$i][0] . " 23:59:59");
            }

            $payments[] = array(
                "payment_date_start" => $payment_date_start,
                "payment_date_end" => $payment_date_end,
                "full_price" => $full_price,
                "children_price" => $children_price,
                "middle_price" => $middle_price,
                "payment_portions" => $payment_portions,
                "associated_discount" => $associated_discount / 100,
            );
        }

        $date_start = explode("/", $date_start);
        $date_start = strval($date_start[2]) . "-" . strval($date_start[1]) . "-" . strval($date_start[0]);

        $date_finish = explode("/", $date_finish);
        $date_finish = strval($date_finish[2]) . "-" . strval($date_finish[1]) . "-" . strval($date_finish[0] . " 23:59:59");

        $date_start_show = explode("/", $date_start_show);
        $date_start_show = strval($date_start_show[2]) . "-" . strval($date_start_show[1]) . "-" . strval($date_start_show[0]);

        $date_finish_show = explode("/", $date_finish_show);
        $date_finish_show = strval($date_finish_show[2]) . "-" . strval($date_finish_show[1]) . "-" . strval($date_finish_show[0] . " 23:59:59");

        $subscribed_male = $this->eventsubscription_model->getSubscriptionsByEventId($event_id, 'capacity_male');
        $capacity_male = $capacity_male + count($subscribed_male);

        $subscribed_female = $this->eventsubscription_model->getSubscriptionsByEventId($event_id, 'capacity_female');
        $capacity_female = $capacity_female + count($subscribed_female);

        $subscribed_nonsleeper = $this->eventsubscription_model->getSubscriptionsByEventId($event_id, 'nonsleeper');
        $capacity_nonsleeper = $capacity_nonsleeper + count($subscribed_nonsleeper);

        if (count($errors) > 0)
            return $this->editEvent($event_id, $errors, $event_name, $description, $date_start, $date_finish, $date_start_show, $date_finish_show, $capacity_male, $capacity_female, $capacity_nonsleeper, $payments);

        try {
            $this->Logger->info("Updating event " . $event_name);
            $this->generic_model->startTransaction();

            $eventId = $this->event_model->updateEvent($event_id, $event_name, $description, $date_start, $date_finish, $date_start_show, $date_finish_show, $enabled, $capacity_male, $capacity_female, $capacity_nonsleeper);

            if ($eventId) {
                $this->event_model->deleteEventPaymentPeriods($event_id);

                foreach ($payments as $payment) {
                    $this->event_model->insertNewPaymentPeriod($event_id, $payment["payment_date_start"], $payment["payment_date_end"], $payment["full_price"], $payment["middle_price"], $payment["children_price"], $payment["associated_discount"], $payment["payment_portions"]);
                }

                $this->generic_model->commitTransaction();
                $this->Logger->info("New event successfully inserted");
                echo "<script>alert('Evento atualizado com sucesso!');opener.location.reload(); window.close();</script>";
                //redirect("events/manageEvents");
            } else
                echo "<script>alert('Ocorreu um erro ao atualizar o evento. Tente novamente.');window.history.back();</script>";
        } catch (Exception $ex) {
            $this->Logger->error("Failed to insert new event");
            $this->generic_model->rollbackTransaction();
            $data['error'] = true;

            echo "<script>alert('Ocorreu um erro ao atualizar o evento. Tente novamente.');window.history.back();</script>";

            //$this->loadReportView('admin/events/event_edit', $data);
        }
    }

    public function manageEvents() {
        $this->Logger->info("Starting " . __METHOD__);

        if (!$this->checkSession())
            redirect("login/index");

        if (!$this->checkPermition(array(COMMON_USER, SECRETARY, SYSTEM_ADMIN))) {
            $this->denyAcess(___METHOD___);
        }

        $events = $this->event_model->getAllEvents();
        $eventsToScreen = array();
        foreach ($events as $event) {
            $payments = $this->event_model->getEventPaymentPeriods($event->getEventId());
            $event->setIsValid($payments);
            $eventsToScreen[] = $event;
        }
        $data["events"] = $eventsToScreen;


        $this->loadReportView("admin/events/manage", $data);
    }

    public function camp() {
        $type = $this->input->get('type', TRUE);
        $data["validation"] = "";
        $data["discount"] = "";
        if ($type) {
            if ($type == "validation")
                $data["validation"] = "selected";
            else if ($type == "discount")
                $data["discount"] = "selected";
        } else
            $data["validation"] = "selected";
        $this->loadView("admin/camps/camp_admin_container", $data);
    }

    public function manageCamps() {
        $data['camps'] = $this->summercamp_model->getAllSummerCamps();
        $this->loadReportView("admin/camps/manage_camps", $data);
    }

    public function createCamp() {
        $data['payments'] = array();

        $this->loadView("admin/camps/insert_camp", $data);
    }

    public function queue() {
        $data = array();
        $years = array();
        $start = 2015;
        $date = date('Y');
        $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        $end = $date;
        while ($campsByYear != null) {
            $end = $date;
            $date++;
            $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        }
        while ($start <= $end) {
            $years[] = $start;
            $start++;
        }
        $year = null;

        if (isset($_GET['ano_f']))
            $year = $_GET['ano_f'];
        else {
            $year = date('Y');
        }

        $data['ano_escolhido'] = $year;
        $data['years'] = $years;

        $campChosen = 0;
        $camps = array(0 => "Colônia Verão", 1 => "Mini Kinderland");

        if (isset($_GET['colonia_f']))
            $campChosen = $_GET['colonia_f'];

        $data['colonia_escolhida'] = $campChosen;
        $data['camps'] = $camps;

        $miniCamp = $campChosen;

        $statusCamps = $this->summercamp_model->getMiniCampsOrNotByYear($year, $miniCamp);

        $campsId = array();

        if ($statusCamps != null) {
            foreach ($statusCamps as $statusCamp) {
                $campsId[] = $statusCamp->getCampId();
            }
        }

        $selected = 2;
        $opcoes = array(0 => "Sócios", 1 => "Não Sócios", 2 => "Todos");

        if (isset($_GET['opcao_f']))
            $selected = $_GET['opcao_f'];

        $data['selecionado'] = $selected;
        $data['opcoes'] = $opcoes;

        $people = array();
        $peopleId = array();
        $peopleFinal = array();
        $campsIdStr = "";
        if ($campsId != null && count($campsId) > 0) {
            $campsIdStr = $campsId[0];
            for ($i = 1; $i < count($campsId); $i++)
                $campsIdStr .= "," . $campsId[$i];
        }
        $people = $this->summercamp_model->getAssociatedOrNotByStatusAndSummerCamp($campsIdStr, $selected);
        $nextPosition = $this->summercamp_model->getNextAvailablePosition($campsIdStr);

        $data['nextPosition'] = $nextPosition;
        $data['people'] = $people;
        $this->loadReportView("admin/camps/queue", $data);
    }

    public function insertNewCamp() {
        $this->Logger->info("Running: " . __METHOD__);
        $camp = new SummerCamp(null, $_POST['camp_name'], null, $_POST['date_start'], $_POST['date_finish'], $_POST['date_start_pre'], $_POST['date_finish_pre'], $_POST['date_start_pre_associate'], $_POST['date_finish_pre_associate'], null, //description
                true, //preEnabled
                $_POST['capacity_male'], $_POST['capacity_female'], $_POST['mini_camp']
        );

        try {
            $this->Logger->info("Inserting new summer camp");
            $this->generic_model->startTransaction();

            $campId = $this->summercamp_model->insertNewCamp($camp);
            /* inserir payment periods */

            $this->generic_model->commitTransaction();
            $this->Logger->info("New summer camp successfully inserted");
            redirect("admin/camp");
        } catch (Exception $ex) {
            $this->Logger->error("Failed to insert new camp");
            $this->generic_model->rollbackTransaction();
            $data['error'] = true;
            $this->loadView('admin/camps/create_error', $data);
        }
    }

    public function changeCampEnabledStatus() {
        $this->Logger->info("Running: " . __METHOD__);
        $campId = $_POST['camp_id'];
        $enabled = ($_POST['status'] == "t") ? true : false;

        try {
            $this->Logger->info("Updating enabled pre-subscriptions");
            $this->generic_model->startTransaction();

            $campId = $this->summercamp_model->updateCampPreEnabled($campId, $enabled);
            /* inserir payment periods */

            $this->generic_model->commitTransaction();
            $this->Logger->info("Updated enabled pre-subscriptions");
            echo "true";
        } catch (Exception $ex) {
            $this->Logger->error("Failed to update enabled pre-subscriptions");
            $this->generic_model->rollbackTransaction();
            echo "false";
        }
    }

    public function validateColonists() {
        $this->Logger->info("Running: " . __METHOD__);

        $data = array();
        $years = array();
        $start = 2015;
        $date = date('Y');
        $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        $end = $date;
        while ($campsByYear != null) {
            $end = $date;
            $date++;
            $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        }
        while ($start <= $end) {
            $years[] = $start;
            $start++;
        }
        $year = null;

        if (isset($_GET['ano_f']))
            $year = $_GET['ano_f'];
        else {
            $year = date('Y');
        }

        $data['ano_escolhido'] = $year;
        $data['years'] = $years;

        $allCamps = $this->summercamp_model->getAllSummerCampsByYear($year);
        $campsQtd = count($allCamps);
        $camps = array();
        $start = $campsQtd;
        $end = 1;

        $campChosen = null;

        if (isset($_GET['colonia_f']))
            $campChosen = $_GET['colonia_f'];

        $campCount = array();
        $count = null;

        $campChosenId = null;
        foreach ($allCamps as $camp) {
            $count = $this->summercamp_model->getCountStatusBySummerCamp($camp->getCampId());

            if ($count != null) {
                $campCount[] = $count;
                $camps[] = $camp->getCampName();
            }

            if ($camp->getCampName() == $campChosen)
                $campChosenId = $camp->getCampId();
        }

        $data['colonia_escolhida'] = $campChosen;
        $data['camps'] = $camps;
        $data['campCount'] = $campCount;

        $statusChosen = 'Aguardando Validação';

        if (isset($_GET['status_f']))
            $statusChosen = $_GET['status_f'];

        $status = array('Aguardando Validação', 'Não Validada', 'Validada', 'Todos');

        $data['status_escolhido'] = $statusChosen;
        $data['status'] = $status;

        if ($statusChosen == 'Aguardando Validação') {
            $data['colonists'] = $this->summercamp_model->getAllColonistsByYearSummerCampAndStatus($year, $campChosenId, 1);
        } else if ($statusChosen == 'Não Validada') {
            $data['colonists'] = $this->summercamp_model->getAllColonistsByYearSummerCampAndStatus($year, $campChosenId, 6);
        } else if ($statusChosen == 'Validada') {
            $data['colonists'] = $this->summercamp_model->getAllColonistsByYearSummerCampAndStatus($year, $campChosenId, 2);
        } else if ($statusChosen == 'Todos') {
            $data['colonists'] = $this->summercamp_model->getAllColonistsByYearSummerCampAndStatus($year, $campChosenId, null);
        }

        $this->loadReportView("admin/camps/validate_colonists", $data);
    }

    public function colonist_exclusion() {
<<<<<<< HEAD
    	$this->Logger->info("Running: " . __METHOD__);
    	$data = array();
    	$years = array();
    	$start = 2015;
    	$date = intval(date('Y'));
    	$campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
    	$end = $date;
    	while ($campsByYear != null) {
    		$end = $date;
    		$date++;
    		$campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
    	}
    	while ($start <= $end) {
    		$years[] = $start;
    		$start++;
    	}
    	$year = null;
    
    	if (isset($_GET['ano_f']))
    		$year = $_GET['ano_f'];
    		else {
    			$year = date('Y');
    		}
    
    		$data['ano_escolhido'] = $year;
    		$data['years'] = $years;
    		
    		$allCamps = $this->summercamp_model->getAllSummerCampsByYear($year);
    		$campsQtd = count($allCamps);
    		$camps = array();
    		$start = $campsQtd;
    		$end = 1;
    		
    		$campChosen = null;
    		
    		if (isset($_GET['colonia_f']))
    			$campChosen = $_GET['colonia_f'];
    		
    			$campCount = array();
    			$count = null;
    		
    			$campChosenId = null;
    			foreach ($allCamps as $camp) {
    				$count = $this->summercamp_model->getCountStatusBySummerCamp($camp->getCampId());
    		
    				if ($count != null) {
    					$campCount[] = $count;
    					$camps[] = $camp->getCampName();
    				}
    		
    				if ($camp->getCampName() == $campChosen)
    					$campChosenId = $camp->getCampId();
    			}
    		
    			$data['colonia_escolhida'] = $campChosen;
    			$data['camps'] = $camps;
    			$data['campCount'] = $campCount;
    
    		$shownStatus = SUMMER_CAMP_SUBSCRIPTION_STATUS_WAITING_VALIDATION . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_FILLING_IN . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_CANCELLED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_EXCLUDED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_GIVEN_UP . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_QUEUE . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_PENDING_PAYMENT . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_SUBSCRIBED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED_WITH_ERRORS;
    
    		$data['colonists'] = $this->summercamp_model->getAllColonistsBySummerCampAndYear($year, $shownStatus);
    		$this->loadReportView("admin/camps/colonist_exclusion", $data);
    }
    
=======
        $this->Logger->info("Running: " . __METHOD__);
        $data = array();
        $years = array();
        $start = 2015;
        $date = intval(date('Y'));
        $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        $end = $date;
        while ($campsByYear != null) {
            $end = $date;
            $date++;
            $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        }
        while ($start <= $end) {
            $years[] = $start;
            $start++;
        }
        $year = null;

        if (isset($_GET['ano_f']))
            $year = $_GET['ano_f'];
        else {
            $year = date('Y');
        }

        $data['ano_escolhido'] = $year;
        $data['years'] = $years;

        $shownStatus = SUMMER_CAMP_SUBSCRIPTION_STATUS_WAITING_VALIDATION . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_FILLING_IN . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_CANCELLED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_EXCLUDED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_GIVEN_UP . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_QUEUE . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_PENDING_PAYMENT . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_SUBSCRIBED . "," . SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED_WITH_ERRORS;

        $data['colonists'] = $this->summercamp_model->getAllColonistsBySummerCampAndYear($year, $shownStatus);
        $this->loadReportView("admin/camps/colonist_exclusion", $data);
    }

>>>>>>> d10b3e0df36e6b3d2ac7a49df90e25dd4ec2dda3
    public function password() {
        $this->Logger->info("Running: " . __METHOD__);


        $pass = $_POST['senha'];
        $id = $this->session->userdata("user_id");
        $person = $this->personuser_model->getUserById($id);
        $login = $person->getLogin();
        $colonistId = $_POST['colonist_id'];
        $summerCampId = $_POST['summer_camp_id'];
        $situation = $_POST['situation'];
        $cancel_reason = $_POST['cancel_reason'];
        $discount = $_POST['discount'];

        $userId = $this->personuser_model->userLogin($login, $pass);


        if ($userId != null) {

            $result = $this->summercamp_model->updateStatus($colonistId, $summerCampId, $situation, $discount, $cancel_reason);
            if ($result != null)
                echo "true";
            else
                echo "false";
        } else
            echo "false";
    }

    public function setDiscount() {
        $data = array();
        $years = array();
        $start = 2015;
        $date = intval(date('Y'));
        $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        $end = $date;
        while ($campsByYear != null) {
            $end = $date;
            $date++;
            $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        }
        while ($start <= $end) {
            $years[] = $start;
            $start++;
        }
        $year = null;

        if (isset($_GET['ano_f']))
            $year = $_GET['ano_f'];
        else {
            $year = date('Y');
        }

        $data['ano_escolhido'] = $year;
        $data['years'] = $years;

        $this->Logger->info("Running: " . __METHOD__);
        $data['colonists'] = $this->summercamp_model->getAllColonistsForDiscount();
        $data['discountReasons'] = $this->summercamp_model->getDefaultDiscountReasons();
        $this->loadReportView("admin/camps/set_discount", $data);
    }

    public function setDiscountValue() {
        $this->Logger->info("Running: " . __METHOD__);
        $colonistId = $this->input->get('colonist_id', TRUE);
        $summerCampId = $this->input->get('summer_camp_id', TRUE);
        $discount_value = $this->input->get('discount_value', TRUE);
        $discount_reason_id = $this->input->get('discount_reason_id', TRUE);
        if ($discount_reason_id == -2) {
            $discount_reason_other = $this->input->get('discount_reason_other', TRUE);
            $discount_reason_id = $this->summercamp_model->insertDiscountReason($discount_reason_other);
        }
        if ($this->summercamp_model->updateDiscount($colonistId, $summerCampId, $discount_value, $discount_reason_id))
            echo "alert('Problema ao modificar o desconto, tente novamente')";
        redirect("admin/setDiscount?type=discount");
    }

    public function paymentLiberation() {
        $data = array();

        $years = array();
        $start = 2015;
        $date = intval(date('Y'));
        $campsByYear = null;
        do {
            $end = $date;
            $date++;
            $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        } while ($campsByYear != null);

        while ($start <= $end) {
            $years[] = $start;
            $start++;
        }
        $data["years"] = $years;

        if (isset($_POST['year'])) {
            $yearChosen = $_POST['year'];
            $data["year_selected"] = $yearChosen;
            if (isset($_POST['camp_id'])) {
                $selectedCamp = $this->summercamp_model->getSummerCampById($_POST['camp_id']);
                if ($selectedCamp != null) {
                    $data["camp_selected_id"] = $selectedCamp->getCampId();
                    $data["camp_selected_name"] = $selectedCamp->getCampName();
                    $data["camp_selected_male_capacity"] = $selectedCamp->getCapacityMale();
                    $data["camp_selected_female_capacity"] = $selectedCamp->getCapacityFemale();

                    $campSubscriptions = $this->summercamp_model->getSummerCampSubscriptionsByStatusAndGender($_POST["camp_id"]);
                    $data["camp_details"] = $campSubscriptions;

                    $subscriptions = $this->summercamp_model->getAllColonistsWithQueueNumberBySummerCamp($selectedCamp->getCampId());
                    if ($subscriptions != null)
                        $data["subscriptions"] = $subscriptions;
                }
            }

            $allCamps = $this->summercamp_model->getAllSummerCampsByYear($yearChosen);
            $data["camps"] = $allCamps;
        } else {
            $allCamps = $this->summercamp_model->getAllSummerCampsByYear(intval(date('Y')));
            $data["camps"] = $allCamps;
            $data["year_selected"] = date('Y');
        }

        $this->loadReportView("admin/camps/payment_liberation", $data);
    }

    public function liberatePayments() {
        $this->Logger->info("Running: " . __METHOD__);
        echo "<script>alert('Ainda nao funcional(Em desenvolvimento).'); window.location.replace('" . $this->config->item('url_link') . "admin/paymentLiberation');</script>";
    }

    public function managePaymentLiberation() {
        $this->Logger->info("Running: " . __METHOD__);

        $data = array();

        $years = array();
        $start = 2015;
        $date = intval(date('Y'));
        $campsByYear = null;
        do {
            $end = $date;
            $date++;
            $campsByYear = $this->summercamp_model->getAllSummerCampsByYear($date);
        } while ($campsByYear != null);

        while ($start <= $end) {
            $years[] = $start;
            $start++;
        }
        $data["years"] = $years;

        if (isset($_POST['year'])) {
            $yearChosen = $_POST['year'];
            $data["year_selected"] = $yearChosen;
            if (isset($_POST['camp_id'])) {
                $selectedCamp = $this->summercamp_model->getSummerCampById($_POST['camp_id']);
                if ($selectedCamp != null) {
                    $data["camp_selected_id"] = $selectedCamp->getCampId();
                    $data["camp_selected_name"] = $selectedCamp->getCampName();
                    $data["camp_selected_male_capacity"] = $selectedCamp->getCapacityMale();
                    $data["camp_selected_female_capacity"] = $selectedCamp->getCapacityFemale();

                    $campSubscriptions = $this->summercamp_model->getSummerCampSubscriptionsByStatusAndGender($_POST["camp_id"]);
                    $data["camp_details"] = $campSubscriptions;

                    $subscriptions = $this->summercamp_model->getAllColonistsWaitingPaymentBySummerCamp($selectedCamp->getCampId());
                    if ($subscriptions != null)
                        $data["subscriptions"] = $subscriptions;
                }
            }

            $allCamps = $this->summercamp_model->getAllSummerCampsByYear($yearChosen);
            $data["camps"] = $allCamps;
        } else {
            $data["year_selected"] = date('Y');
            $allCamps = $this->summercamp_model->getAllSummerCampsByYear(intval(date('Y')));
            $data["camps"] = $allCamps;
        }

        $this->loadReportView("admin/camps/manage_payment_liberation", $data);
    }

    public function updateColonistValidation() {
        $this->Logger->info("Running: " . __METHOD__);
        $colonistId = $_POST['colonist_id'];
        $summerCampId = $_POST['summer_camp_id'];

        $genderOk = (isset($_POST['gender'])) ? $_POST['gender'] : null;
        $pictureOk = (isset($_POST['picture'])) ? $_POST['picture'] : null;
        $identityOk = (isset($_POST['identity'])) ? $_POST['identity'] : null;
        $birthdayOk = (isset($_POST['birthday'])) ? $_POST['birthday'] : null;
        $parentsNameOk = (isset($_POST['parents_name'])) ? $_POST['parents_name'] : null;
        $colonistNameOk = (isset($_POST['colonist_name'])) ? $_POST['colonist_name'] : null;

        $msgGender = ($genderOk == "false") ? $_POST['msg_gender'] : "";
        $msgPicture = ($pictureOk == "false") ? $_POST['msg_picture'] : "";
        $msgIdentity = ($identityOk == "false") ? $_POST['msg_identity'] : "";
        $msgBirthdate = ($birthdayOk == "false") ? $_POST['msg_birthday'] : "";
        $msgParentsName = ($parentsNameOk == "false") ? $_POST['msg_parents_name'] : "";
        $msgColonistName = ($colonistNameOk == "false") ? $_POST['msg_colonist_name'] : "";

        $validationReturn = $this->validation_model->updateColonistValidation($colonistId, $summerCampId, $genderOk, $pictureOk, $identityOk, $birthdayOk, $parentsNameOk, $colonistNameOk, $msgGender, $msgPicture, $msgIdentity, $msgBirthdate, $msgParentsName, $msgColonistName);

        if ($validationReturn)
            echo "true";
        else
            echo "false";
    }

    public function confirmValidation() {
        $this->Logger->info("Running: " . __METHOD__);
        $colonistId = $_POST['colonist_id'];
        $summerCampId = $_POST['summer_camp_id'];
        $gender = $_POST['gender'];
        $picture = $_POST['picture'];
        $identity = $_POST['identity'];
        $birthday = $_POST['birthday'];
        $parentsName = $_POST['parents_name'];
        $colonistName = $_POST['colonist_name'];

        $this->Logger->info("User validating this colonist[id: " . $colonistId . "] -> User: " . $this->session->userdata("fullname") . "[user id: " . $this->session->userdata("user_id") . "]");
        $summerCampSubscription = $this->summercamp_model->getSummerCampSubscription($colonistId, $summerCampId);
        if ($summerCampSubscription->getSituationId() == SUMMER_CAMP_SUBSCRIPTION_STATUS_FILLING_IN) {
            $this->Logger->error("Cannot validate because the colonist returned to filling in status");
            return;
        }

        $status = 0;
        if ($gender == "true" && $picture == "true" && $identity == "true" && $birthday == "true" && $parentsName == "true" && $colonistName == "true")
            $status = SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED;
        else
            $status = SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED_WITH_ERRORS;

        $this->summercamp_model->updateColonistStatus($colonistId, $summerCampId, $status);
        if ($status == SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED)
            $this->sendValidatedEmail($colonistId, $summerCampId);
        else
            $this->sendNotValidatedEmail($colonistId, $summerCampId);
        echo $this->summercamp_model->getStatusDescription($status);
    }

    public function sendNotValidatedEmail($colonistId, $summerCampId) {
        $this->Logger->info("Running: " . __METHOD__);

        $summercamp = $this->summercamp_model->getSummerCampById($summerCampId);
        if (!$summercamp) {
            $this->Logger->error("Camp not found, cannot send an email");
            return;
        }

        $colonist = $this->colonist_model->getColonist($colonistId);
        if (!$colonist) {
            $this->Logger->error("Colonist not found");
            return;
        }

        $personuser = $this->colonist_model->getColonistPersonUser($colonistId, $summerCampId);
        if (!$personuser) {
            $this->Logger->error("PersonUser related to colonist not found");
            return;
        }

        $this->Logger->info("Sending email");

        $responsableId = $personuser->getPersonId();

        $father = $this->summercamp_model->getParentIdOfSummerCampSubscripted($summerCampId, $colonistId, "Pai");
        $mother = $this->summercamp_model->getParentIdOfSummerCampSubscripted($summerCampId, $colonistId, "Mãe");

        $emailArray = array();
        if ($father && $responsableId != $father) {
            $father = $this->person_model->getPersonFullById($father);
            $emailArray[] = $father->email;
        }
        if ($mother && $mother != $responsableId) {
            $mother = $this->person_model->getPersonFullById($mother);
            $emailArray[] = $mother->email;
        }


        $this->sendValidationWithErrorsEmail($personuser, $colonist, $summercamp->getCampName(), $emailArray);
    }

    public function sendValidatedEmail($colonistId, $summerCampId) {
        $this->Logger->info("Running: " . __METHOD__);

        $summercamp = $this->summercamp_model->getSummerCampById($summerCampId);
        if (!$summercamp) {
            $this->Logger->error("Camp not found, cannot send an email");
            return;
        }

        $colonist = $this->colonist_model->getColonist($colonistId);
        if (!$colonist) {
            $this->Logger->error("Colonist not found");
            return;
        }

        $personuser = $this->colonist_model->getColonistPersonUser($colonistId, $summerCampId);
        if (!$personuser) {
            $this->Logger->error("PersonUser related to colonist not found");
            return;
        }

        $this->Logger->info("Sending email");

        $responsableId = $personuser->getPersonId();

        $father = $this->summercamp_model->getParentIdOfSummerCampSubscripted($summerCampId, $colonistId, "Pai");
        $mother = $this->summercamp_model->getParentIdOfSummerCampSubscripted($summerCampId, $colonistId, "Mãe");

        $emailArray = array();
        if ($father && $responsableId != $father) {
            $father = $this->person_model->getPersonFullById($father);
            $emailArray[] = $father->email;
        }
        if ($mother && $mother != $responsableId) {
            $mother = $this->person_model->getPersonFullById($mother);
            $emailArray[] = $mother->email;
        }


        $this->sendValidationOkEmail($personuser, $colonist, $summercamp->getCampName(), $emailArray);
    }

    public function users() {
        $this->Logger->info("Running: " . __METHOD__);
        $this->loadView("admin/users/user_admin_container");
    }

    public function userPermissions() {
        $this->Logger->info("Running: " . __METHOD__);
        $data['users'] = $this->person_model->getUserPermissionsDetailed();
        $this->loadReportView("admin/users/user_permissions", $data);
    }

    public function updatePersonPermissions() {
        $this->Logger->info("Running: " . __METHOD__);

        $this->Logger->info("Array POST: " . print_r($_POST, true));
        $personId = $_POST['person_id'];

        $arrNewPermissions = array(
            'system_admin' => isset($_POST['system_admin']),
            'director' => isset($_POST['director']),
            'secretary' => isset($_POST['secretary']),
            'coordinator' => isset($_POST['coordinator']),
            'doctor' => isset($_POST['doctor']),
            'monitor' => isset($_POST['monitor_instructor'])
        );

        $this->Logger->info("Array New Permissions: " . print_r($arrNewPermissions, true));
        try {
            $this->Logger->info("Updating user's permissions");
            $this->generic_model->startTransaction();

            $this->person_model->updateUserPermissions($personId, $arrNewPermissions);

            $this->generic_model->commitTransaction();
            $this->Logger->info("Updated user's permissions");
        } catch (Exception $ex) {
            $this->Logger->error("Failed to update user's permissions");
            $this->generic_model->rollbackTransaction();
        }

        redirect("admin/userPermissions");
    }

    public function viewColonistInfo() {
        $this->Logger->info("Starting " . __METHOD__);
        if (($this->input->get('type', TRUE)) !== null) {
            $type = $this->input->get('type', TRUE);
        } else {
            $type = null;
        }
        $data['type'] = $type;
        $colonistId = $this->input->get('colonistId', TRUE);
        $summerCampId = $this->input->get('summerCampId', TRUE);
        $camper = $this->summercamp_model->getSummerCampSubscription($colonistId, $summerCampId);
        $address = $this->address_model->getAddressByPersonId($camper->getPersonId());
        $responsableId = $camper->getPersonUserId();
        $responsableAddress = $this->address_model->getAddressByPersonId($responsableId);
        $data["sameAddressResponsable"] = "n";
        if ($responsableAddress)
            if ($address->getAddressId() == $responsableAddress->getAddressId())
                $data["sameAddressResponsable"] = "s";
        $data["colonistId"] = $colonistId;
        $data["summerCamp"] = $this->summercamp_model->getSummerCampById($summerCampId);
        $data["id"] = $summerCampId;
        $data["fullName"] = $camper->getFullName();
        $data["Gender"] = $camper->getGender();
        $data["birthdate"] = date("d-m-Y", strtotime($camper->getBirthDate()));
        $data["school"] = $camper->getSchool();
        $data["schoolYear"] = $camper->getSchoolYear();
        $data["documentNumber"] = $camper->getDocumentNumber();
        $data["documentType"] = $camper->getDocumentType();
        $data["phone1"] = $camper->getDocumentType();
        $data["phone2"] = $camper->getDocumentType();
        $data["street"] = $address->getStreet();
        $data["number"] = $address->getPlaceNumber();
        $data["city"] = $address->getCity();
        $data["cep"] = $address->getCEP();
        $data["complement"] = $address->getComplement();
        $data["neighborhood"] = $address->getNeighborhood();
        $data["uf"] = $address->getUf();
        $telephones = $this->telephone_model->getTelephonesByPersonId($camper->getPersonId());
        $data["phone1"] = isset($telephones[0]) ? $telephones[0] : FALSE;
        $data["phone2"] = isset($telephones[1]) ? $telephones[1] : FALSE;
        $father = $this->summercamp_model->getParentIdOfSummerCampSubscripted($summerCampId, $colonistId, "Pai");
        $mother = $this->summercamp_model->getParentIdOfSummerCampSubscripted($summerCampId, $colonistId, "Mãe");
        if ($father) {
            if ($father == $responsableId)
                $data["responsableDadMother"] = "dad";
            $father = $this->person_model->getPersonFullById($father);
            $data["dadFullName"] = $father->fullname;
            $data["dadEmail"] = $father->email;
            $data["dadPhone"] = $father->phone1;
        } else {
            $data["noFather"] = TRUE;
        }
        if ($mother) {
            if ($mother == $responsableId)
                $data["responsableDadMother"] = "mother";
            $mother = $this->person_model->getPersonFullById($mother);
            $data["motherFullName"] = $mother->fullname;
            $data["motherEmail"] = $mother->email;
            $data["motherPhone"] = $mother->phone1;
        } else {
            $data["noMother"] = TRUE;
        }
        if ($camper->getRoommate1())
            $data['roommate1'] = $camper->getRoommate1();
        if ($camper->getRoommate2())
            $data['roommate2'] = $camper->getRoommate2();
        if ($camper->getRoommate3())
            $data['roommate3'] = $camper->getRoommate3();
        if ($data["summerCamp"]->isMiniCamp()) {
            $miniCamp = $this->summercamp_model->getMiniCampObs($summerCampId, $colonistId);
            $data['miniCamp'] = $miniCamp;
        }
        $this->loadView('summercamps/viewColonistInfo', $data);
    }

    public function verifyDocument() {
        $this->Logger->info("Starting " . __METHOD__);
        $camp_id = $this->input->get('camp_id', TRUE);
        $colonist_id = $this->input->get('colonist_id', TRUE);
        $document_type = $this->input->get('document_type', TRUE);
        $document = $this->summercamp_model->getNewestDocument($camp_id, $colonist_id, $document_type);
        if ($document) {
            if (strtolower($document["extension"]) == "pdf")
                header("Content-type: application/pdf");
            else
                header("Content-type: image/jpeg");
            echo pg_unescape_bytea($document["data"]);
        } else {
            $this->loadView("admin/users/documentNotFound");
        }
    }

    public function viewEmails($userId) {
        $this->Logger->info("Starting " . __METHOD__);
        $person = $this->person_model->getPersonById($userId);
        $emails = $this->email_model->getEmailsSentToUserById($userId);

        $data['emails'] = $emails;
        $data['person'] = $person;

        $this->loadView("admin/users/emailsSent", $data);
    }

    public function writeEmail($userId) {
        $this->Logger->info("Starting " . __METHOD__);
        $person = $this->person_model->getPersonById($userId);

        $data['person'] = $person;

        $this->loadView("admin/users/writeEmail", $data);
    }

    public function sendEmail() {
        $this->Logger->info("Starting " . __METHOD__);
        $userId = $this->input->post('user_id', TRUE);
        $userEmail = $this->input->post('user_email', TRUE);
        $subject = $this->input->post('subject', TRUE);
        $message = $this->input->post('message', TRUE);

        $person = $this->person_model->getPersonById($userId);

        if ($this->sendMail($subject, $message, $person)) {
            echo "<script>alert('Envio realizado com sucesso'); window.location.replace('" . $this->config->item('url_link') . "admin/viewEmails/" . $userId . "');</script>";
        } else {
            echo "<script>alert('Houve um problema ao enviar o email. Por favor, tente novamente.'); window.history.back(-1);</script>";
        }
    }

    public function updateQueueNumber() {
        $this->Logger->info("Starting " . __METHOD__);
        $userId = $this->input->post('user_id', TRUE);
        $summerCampType = $this->input->post('summer_camp_type', TRUE);
        $yearSelected = $this->input->post('year', TRUE);
        $position = $this->input->post('position', TRUE);

        try {
            $this->Logger->info("Getting summer camps id");
            $summerCamps = $this->summercamp_model->getMiniCampsOrNotByYear($yearSelected, $summerCampType);
            $campsIdStr = "";
            if ($summerCamps != null && count($summerCamps) > 0) {
                $campsIdStr = $summerCamps[0]->getCampId();
                for ($i = 1; $i < count($summerCamps); $i++)
                    $campsIdStr .= "," . $summerCamps[$i]->getCampId();
            }
            $this->Logger->debug("Summer Camp Ids: " . $campsIdStr);
            if (strlen($campsIdStr) == 0)
                throw new Exception("Nenhuma colonia encontrada com os parametros dados.");

            $this->Logger->info("Checking if the given position is already occupied by another person");
            if (!$this->summercamp_model->checkQueueNumberAvailability($userId, $campsIdStr, $position))
                throw new Exception("Falha ao atualizar fila de espera, verifique se outra pessoa possui o mesmo valor");

            $this->Logger->info("Updating queue number in database");
            $this->generic_model->startTransaction();
            if (!$this->summercamp_model->updateQueueNumber($userId, $campsIdStr, $position))
                throw new Exception("Falha ao atualizar o banco de dados");
            $this->generic_model->commitTransaction();

            echo "true";
        } catch (Exception $ex) {
            $this->Logger->error("Failed to insert new user");
            $this->generic_model->rollbackTransaction();
            echo utf8_decode($ex->getMessage());
        }
    }

    public function updateToWaitingPaymentIndividual() {
        $this->Logger->info("Starting " . __METHOD__);
        $colonistId = $this->input->post('colonist_id', TRUE);
        $summerCampId = $this->input->post('summer_camp_id', TRUE);

        try {
            $this->generic_model->startTransaction();
            $summerCamp = $this->summercamp_model->getSummerCampById($summerCampId);

            $status = SUMMER_CAMP_SUBSCRIPTION_STATUS_PENDING_PAYMENT . ", " . SUMMER_CAMP_SUBSCRIPTION_STATUS_SUBSCRIBED;
            $countSubs = $this->summercamp_model->getCountSubscriptionsBySummerCampAndStatus($summerCamp->getCampId(), $status);

            $colonist = $this->colonist_model->getColonist($colonistId);

            foreach ($countSubs as $countGender) {
                if ($countGender->gender == $colonist->getGender()) {
                    if ($colonist->getGender() == "M") {
                        if ($summerCamp->getCapacityMale() <= $countGender->count) {
                            echo "Sem vagas para liberar pagamento no masculino.";
                            return;
                        }
                    } else {
                        if ($summerCamp->getCapacityFemale() <= $countGender->count) {
                            echo "Sem vagas para liberar pagamento no feminino.";
                            return;
                        }
                    }
                }
            }

            if (!$this->summercamp_model->updateColonistToWaitingPayment($colonistId, $summerCampId))
                throw new Exception("Falha ao mudar status de colonista.");

            $this->generic_model->commitTransaction();

            $summerCampSub = $this->summercamp_model->getSummerCampSubscription($colonistId, $summerCampId);
            $personuser = $this->colonist_model->getColonistPersonUser($colonistId, $summerCampId);

            $this->sendPaymentLiberationEmail($personuser, $colonist, $summerCamp->getCampName(), $summerCampSub->getDatePaymentLimitFormatted());

            echo "true";
        } catch (Exception $ex) {
            $this->Logger->error("Failed to update user status to waiting payment");
            $this->generic_model->rollbackTransaction();
            echo utf8_decode($ex->getMessage());
        }
    }

    public function cancelSubscriptionIndividual() {
        $this->Logger->info("Starting " . __METHOD__);
        $colonistId = $this->input->post('colonist_id', TRUE);
        $summerCampId = $this->input->post('summer_camp_id', TRUE);

        try {
            $this->generic_model->startTransaction();
            if (!$this->summercamp_model->updateColonistStatus($colonistId, $summerCampId, SUMMER_CAMP_SUBSCRIPTION_STATUS_CANCELLED))
                throw new Exception("Falha ao mudar status de colonista.");

            $this->generic_model->commitTransaction();

            // Enviar email?

            echo "true";
        } catch (Exception $ex) {
            $this->Logger->error("Failed to update user status to waiting payment");
            $this->generic_model->rollbackTransaction();
            echo utf8_decode($ex->getMessage());
        }
    }

    public function updateDatePaymentLimit() {
        $this->Logger->info("Starting " . __METHOD__);
        $colonistId = $this->input->post('colonist_id', TRUE);
        $summerCampId = $this->input->post('summer_camp_id', TRUE);
        $dateLimit = $this->input->post('date_limit', TRUE);

        try {
            $this->generic_model->startTransaction();
            if (!$this->summercamp_model->updateDatePaymentLimit($colonistId, $summerCampId, $dateLimit))
                throw new Exception("Falha ao mudar data de prazo.");

            $this->generic_model->commitTransaction();

            //Enviar Email??

            echo "true";
        } catch (Exception $ex) {
            $this->Logger->error("Failed to update user status to waiting payment");
            $this->generic_model->rollbackTransaction();
            echo utf8_decode($ex->getMessage());
        }
    }

    public function editColonist() {
        print_r($this->input->post('personId', TRUE));
        $this->Logger->info("Starting " . __METHOD__);
        $colonistId = $this->input->post('colonistId', TRUE);
        $summerCampId = $this->input->post('summerCampId', TRUE);
        $personId = $this->input->post('personId', TRUE);
        $birthdate = $this->input->post('birthdate', TRUE);
        $documentType = $this->input->post('documentType', TRUE);
        $documentNumber = $this->input->post('documentNumber', TRUE);
        try {
            $this->Logger->info("Editing colonist $summerCampId");
            $this->generic_model->startTransaction();
            $this->colonist_model->updateColonist($personId, $birthdate, $documentNumber, $documentType, $colonistId);
            $this->generic_model->commitTransaction();
            $this->Logger->info("Colonist sucessfully edited");
            redirect("admin/camp");
        } catch (Exception $ex) {
            $this->Logger->error("Failed to edit colonist subscription");
            $this->generic_model->rollbackTransaction();
        }
    }

    public function editColonistForm() {
        $this->Logger->info("Starting " . __METHOD__);
        $colonistId = $this->input->get('colonistId', TRUE);
        $summerCampId = $this->input->get('summerCampId', TRUE);
        $camper = $this->summercamp_model->getSummerCampSubscription($colonistId, $summerCampId);
        $data["fullName"] = $camper->getFullName();
        $data["summerCampId"] = $summerCampId;
        $data["colonistId"] = $colonistId;
        $data["personId"] = $camper->getPersonId();
        $data["birthdate"] = date("d-m-Y", strtotime($camper->getBirthDate()));
        $data["documentNumber"] = $camper->getDocumentNumber();
        $data["documentType"] = $camper->getDocumentType();
        $this->loadView('admin/camps/edit_colonist_form', $data);
    }

}
