<?php

function setPage($userID, $page = '-'){
    global $sql;
    $sth = $sql->prepare('UPDATE Utenti SET Stato = :page WHERE ID = ' . $userID);
    $sth->bindParam(':page', $page, PDO::PARAM_STR, 7);
    $sth->execute();
}

function getStatus($userID){
    global $sql;
    $sth = $sql->prepare('SELECT * FROM Utenti WHERE ID = ' . $userID);
    $sth->execute();
    $res = $sth->fetch(PDO::FETCH_ASSOC);
    $stato = $res['Stato'];
    return $stato;
}

function isLogged($userID){
    global $sql;
    $sth = $sql->prepare('SELECT * FROM Utenti WHERE ID = ' . $userID);
    $sth->execute();
    $res = $sth->fetch(PDO::FETCH_ASSOC);
    $logged = $res['LoggedIn'];
    if($logged == "Si") return true;
    else return false;
}

function getMonth($month){
    $month = intval($month);
    if($month == 1) return 'gennaio';
    if($month == 2) return 'febbraio';
    if($month == 3) return 'marzo';
    if($month == 4) return 'aprile';
    if($month == 5) return 'maggio';
    if($month == 6) return 'giugno';
    if($month == 7) return 'luglio';
    if($month == 8) return 'agosto';
    if($month == 9) return 'settembre';
    if($month == 10) return 'ottobre';
    if($month == 11) return 'novembre';
    if($month == 12) return 'dicembre';
}

function getNumberEmoji($number){
    if($number == 10){
        return "\xf0\x9f\x94\x9f";
        exit;
    }
    $split = str_split(strval($number));
    $emoji = "";
    foreach($split as $cipher){
        if($cipher == "0") $emoji .= "\x30\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "1") $emoji .= "\x31\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "2") $emoji .= "\x32\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "3") $emoji .= "\x33\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "4") $emoji .= "\x34\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "5") $emoji .= "\x35\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "6") $emoji .= "\x36\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "7") $emoji .= "\x37\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "8") $emoji .= "\x38\xef\xb8\x8f\xe2\x83\xa3";
        if($cipher == "9") $emoji .= "\x39\xef\xb8\x8f\xe2\x83\xa3";
    }
    return $emoji;
}

require_once 'argoapi.php';
if($update){
    $kbb[] = array(
        array(
            "text" => "\xf0\x9f\x94\x90 Effettua il login",
            "callback_data" => "Login"
        )
    );
    $q = $sql->prepare('SELECT * FROM Utenti WHERE ID = :id');
    $q->execute(array(':id' => $userID));
    $res = $q->fetch(PDO::FETCH_ASSOC);
    if($res['LoggedIn'] == "Si") {
        if ($res['AuthToken'] != "-") {
            try {
                $user = new argoUser($res['SchoolCode'], $res['Username'], $res['AuthToken'], 1);
            }
            catch (Exception $e) {
                sm($chatID, "\xe2\x8f\xb0 <b>Sessione scaduta</b>\nLa tua sessione è scaduta, potrebbe essere per un errore nelle credenziali. Ri-effettua il login.", $kbb);
            }

        }
    }
}


if($msg == "/start")
{
    if(isLogged($userID)){
        $panel[] = array(
            array(
                "text" => "\xf0\x9f\x93\x85 Sommario di oggi",
                "callback_data" => "Today"
            )
        );
        $panel[] = array(
            array(
                "text" => "\xf0\x9f\x93\x9a Compiti",
                "callback_data" => "Homeworks"
            ),
            array(
                "text" => "\xf0\x9f\x96\x8a Voti",
                "callback_data" => "GradesList"
            )
        );
        $panel[] = array(
            array(
                "text" => "\xf0\x9f\x98\x95 Note disciplinari",
                "callback_data" => "Notes"
            ),
            array(
                "text" => "\xf0\x9f\x93\x9d Promemoria",
                "callback_data" => "Memos"
            )
        );
        $panel[] = array(
            array(
                "text" => "\xf0\x9f\x91\xa4 Profilo",
                "callback_data" => "Profile"
            ),
            array(
                "text" => "\xe2\x9a\x99\xef\xb8\x8f Impostazioni",
                "callback_data" => "Settings"
            )
        );
        sm($chatID, "\xf0\x9f\x93\x98 <b>Benvenuto nel pannello!</b>\nScegli cosa vuoi fare.\n\n\xe2\x84\xb9\xef\xb8\x8f <i>Hai già effettuato il login, se vuoi disconnetterti clicca su \"Profilo\", e poi su \"Logout\".</i>", $panel);
    }
    else
    {
        $kb[] = array(
            array(
                "text" => "\xf0\x9f\x94\x90 Effettua il login",
                "callback_data" => "Login"
            )
        );
        $kb[] = array(
            array(
                "text" => "\xe2\x84\xb9\xef\xb8\x8f Informazioni",
                "callback_data" => "Informazioni"
            )
        );
        sm($chatID, "\xf0\x9f\x93\x9a <b>Ciao!</b>\n<b>Benvenuto su PArgoBot</b>!\n\nQuesto bot ti permette di vedere i tuoi <b>voti</b>, <b>compiti</b> e tutto ciò che vedresti sul sito di Argo direttamente da <b>Telegram!</b>\n\nPer iniziare, clicca il pulsante qui sotto per <b>effetturare il login</b>.", $kb);
        $q = $sql->prepare('SELECT * FROM Utenti WHERE ID = :id');
        $q->execute(array(':id' => $userID));
        if ($q->rowCount() == 0) {
            $q = $sql->prepare('INSERT INTO Utenti(ID) VALUES(:id)');
            $q->execute(array(':id' => $userID));
        }
    }
}
