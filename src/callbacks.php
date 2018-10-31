<?php

$cbtext = "\xf0\x9f\x91\x8c\xf0\x9f\x8f\xbb Ok!";

if($cbdata == "Informazioni"){
    $kb[] = array(
        array(
            "text" => "\xf0\x9f\x8f\xa1 Torna al menu",
            "callback_data" => "Start"
        )
    );
    cb_reply($cbid, $cbtext, false, $cbmid, "\xf0\x9f\xa4\x94 <b>Informazioni e F.A.Q.</b>\n\n<b>Le password vengono salvate?</b>\nNo, le password non vengono salvate, come puoi vedere dal <a href='https://github.com/iDoppioclick/ArgoBot'>codice sorgente</a>. Una volta effettuato il login viene creata una sessione, e viene salvato solo il token univoco.\n\n<b>Lo sviluppatore può vedere la mia password, voti o qualsiasi altro dato personale?</b>\nAssolutamente no, ma per sicurezza consiglio sempre, quando si effettua il login, di eliminare il messaggio contenente la password, in modo tale da non permettere a nessun altro di vederla.\n\n<b>Posso contribuire al bot?</b>\nOvviamente. Puoi inviare una pull request (Richiesta di modifica del codice) nella pagina del bot su <a href='https://github.com/iDoppioclick/ArgoBot'>GitHub</a>.", $kb, true);
}

if($cbdata == "Start") {
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
    cb_reply($cbid, $cbtext, false, $cbmid, "\xf0\x9f\x93\x9a <b>Ciao!</b>\n<b>Benvenuto su PArgoBot</b>!\n\nQuesto bot ti permette di vedere i tuoi <b>voti</b>, <b>compiti</b> e tutto ciò che vedresti sul sito di Argo direttamente da <b>Telegram!</b>\n\nPer iniziare, clicca il pulsante qui sotto per <b>effetturare il login</b>.", $kb);
}

if($cbdata == "Login"){
    setPage($userID, 'SendSchoolCode');
    $kb[] = array(
        array(
            "text" => "\xe2\x81\x89\xef\xb8\x8f Come lo ottengo?",
            "callback_data" => "GetSchoolCode|$cbmid"
        )
    );
    $kb[] = array(
        array(
            "text" => "\xe2\x9d\x8c Annulla",
            "callback_data" => "AnnullaLogin"
        )
    );
    cb_reply($cbid, $cbtext, false, $cbmid, "\xe2\x8c\xa8\xef\xb8\x8f <b>Inviami ora il codice scuola.</b>\nQuesto sarà necessario per effettuare il login con la tua scuola.", $kb);
}

if($cbdata == "LoginIMG"){
    delete($userID, $cbmid);
    setPage($userID, 'SendSchoolCode');
    $kb[] = array(
        array(
            "text" => "\xe2\x81\x89\xef\xb8\x8f Come lo ottengo?",
            "callback_data" => "GetSchoolCode|$cbmid"
        )
    );
    $kb[] = array(
        array(
            "text" => "\xe2\x9d\x8c Annulla",
            "callback_data" => "AnnullaLogin"
        )
    );
    sm($chatID, "\xe2\x8c\xa8\xef\xb8\x8f <b>Inviami ora il codice scuola.</b>\nQuesto sarà necessario per effettuare il login con la tua scuola.", $kb);
}

if($cbdata && explode("|", $cbdata)[0] == "GetSchoolCode"){
    $menu[] = array(
        array(
            "text" => "\xf0\x9f\x94\x99 Indietro",
            "callback_data" => "LoginIMG"
        )
    );
    setPage($userID);
    delete($userID, $cbmid);
    si($chatID, 'AgADBAADgasxG7NbIFN9fvCm1v7lfAe94RkABB1kCBbn9kfhElADAAEC', $menu, "Il codice scuola è l'elemento evidenziato nella foto.\nPer ottenerlo, vai nella pagina di login della tua scuola, e cerca la dicitura \"Codice scuola\".");
}

if($cbdata == "AnnullaLogin"){
    $qq = $sql->prepare('DELETE FROM Utenti WHERE ID = '.$userID);
    $qq->execute();
    cb_reply($cbid, $cbtext, false, $cbmid, '<b>Annullato.</b>');
    sleep(2);
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
    cb_reply($cbid, $cbtext, false, $cbmid, "\xf0\x9f\x93\x9a <b>Ciao!</b>\n<b>Benvenuto su PArgoBot</b>!\n\nQuesto bot ti permette di vedere i tuoi <b>voti</b>, <b>compiti</b> e tutto ciò che vedresti sul sito di Argo direttamente da <b>Telegram!</b>\n\nPer iniziare, clicca il pulsante qui sotto per <b>effetturare il login</b>.", $kb);
    $q = $sql->prepare('SELECT * FROM Utenti WHERE ID = :id');
    $q->execute(array(':id' => $userID));
    if($q->rowCount() == 0){
        $q = $sql->prepare('INSERT INTO Utenti(ID) VALUES(:id)');
        $q->execute(array(':id' => $userID));
    }
}

if($msg) {
    switch(getStatus($userID)) {
        case 'SendSchoolCode':
            $kb[] = array(
                array(
                "text" => "\xe2\x9d\x8c Annulla",
                "callback_data" => "AnnullaLogin"
                )
            );
            if (preg_match('/S(.*?)[0-9][0-9][0-9][0-9][0-9]/is', $msg, $matches)) {
                sm($chatID, "\xf0\x9f\x91\xa4 <b>Inviami ora l'username di Argo.</b>\nQuesto è l'unico dato che verrà salvato nel database oltre al codice scuola.", $kb);
                setPage($userID, 'SendUsername');
                $q = $sql->prepare('UPDATE Utenti SET SchoolCode = :sc WHERE ID = :id');
                $q->execute(array(':id' => $userID, ':sc' => strtoupper($msg)));
            }
            else
            {
                sm($chatID, "\xe2\x9c\x8b\xf0\x9f\x8f\xbb <b>Errore</b>\nIl codice scuola non è valido. Deve essere nel formato SGXXXXX.", $kb);
            }
        break;

        case 'SendUsername':
            $kb[] = array(
                array(
                    "text" => "\xf0\x9f\x94\x99 Indietro",
                    "callback_data" => "Login"
                )
            );
            if (!preg_match('/\s/', $msg)) {
                sm($chatID, "\xf0\x9f\x94\x90 <b>Inviami ora la password di Argo.</b>\nNon preoccuparti, questo dato <b>non</b> verrà salvato, verrà solo utilizzato per fare il login.\n\n<i>\xe2\x9a\xa0\xef\xb8\x8f Per una maggiore sicurezza, comunque, elimina il messaggio contenente la password dopo averla inviata.</i>", $kb);
                $q = $sql->prepare('UPDATE Utenti SET Username = :sc WHERE ID = :id');
                $q->execute(array(':id' => $userID, ':sc' => $msg));
                setPage($userID, 'SendPassword');
            }
            else
            {
                sm($chatID, "\xe2\x9c\x8b\xf0\x9f\x8f\xbb <b>Errore</b>\nL'username non è valido. Non può contenere spazi.", $kb);
            }
            break;

        case 'SendPassword':
            $kbbb[] = array(
                array(
                    "text" => "\xf0\x9f\x94\x99 Riprova",
                    "callback_data" => "Login"
                )
            );
            $logged[] = array(array("text" => "Vai al pannello", "callback_data" => "Panel"));
            $json = sm($chatID, "<b>\xf0\x9f\xa4\x9e\xf0\x9f\x8f\xbb Provo ad effettuare il login...</b>\nAttendi.", $kb);
            setPage($userID);
            $MessageID = json_decode($json, TRUE)['result']['message_id'];
            $q = $sql->prepare('SELECT * FROM Utenti WHERE ID = :id');
            $q->execute(array(':id' => $userID));
            $res = $q->fetch(PDO::FETCH_ASSOC);
            sleep(2);
            try {
                $user = new argoUser($res['SchoolCode'], $res['Username'], $msg, 0);
                cb_reply($cbid, '', false, $MessageID, "<b>\xf0\x9f\x91\x8c\xf0\x9f\x8f\xbb Loggato correttamente!</b>", $logged);
                $q = $sql->prepare('UPDATE Utenti SET LoggedIn = :sc WHERE ID = :id');
                $q->execute(array(':id' => $userID, ':sc' => 'Si'));
                $q = $sql->prepare('UPDATE Utenti SET AuthToken = :token WHERE ID = :id');
                $q->execute(array(':id' => $userID, ':token' => $user->authToken));
            }
            catch (Exception $e) {
                if(strpos(" ".$e, 'Unable to login')) cb_reply($cbid, '', false, $MessageID, "\xe2\x9d\x8c <b>Errore nelle credenziali</b>\nCi potrebbe essere un errore nelle credenziali, riprova.", $kbbb);
            }

            break;


    }
}

if($cbdata == "Panel"){
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
        cb_reply($cbid, $cbtext, false, $cbmid, "\xf0\x9f\x93\x98 <b>Benvenuto nel pannello!</b>\nScegli cosa vuoi fare.\n\n\xe2\x84\xb9\xef\xb8\x8f <i>Hai effettuato il login, se vuoi disconnetterti clicca su \"Profilo\", e poi su \"Logout\".</i>", $panel);

    }
}

if($cbdata == "Notes"){
    $tastiera[] = array(
        array(
            "text" => "\xf0\x9f\x94\x84 Aggiorna",
            "callback_data" => "Notes"
        )
    );
    $tastiera[] = array(
        array(
            "text" => "\xf0\x9f\x94\x99 Torna indietro",
            "callback_data" => "Panel"
        )
    );
    $notes = $user->noteDisciplinari();
    $notesText = "";
    if(count($notes) == 0){
        $notesText .= "Non ci sono note disciplinari.";
    }else {
        foreach ($notes as $note) {
            $docente = strtolower(str_replace(")", "", str_replace("(Prof. ", "", $note['docente'])));
            $cognome = ucfirst(explode(' ', $docente)[0]);
            $nome = strtoupper(substr(explode(' ', $docente)[1], 0, 1).".");
            $data = explode('-', $note['datNota']);
            $giorno = $data[2];
            $mese = getMonth($data[1]);
            $anno = $data[0];
            if($giorno == 1 || $giorno == 11) $article = "l'";
            else $article = "il ";
            $seen = $note['flgVisualizzata'];
            if($seen == "S") $seen = "\xe2\x98\x91";
            else $seen = "\xe2\x9d\x8c";
            $notesText .= "- ".$note['desNota']."\nInserita da <b>$cognome $nome</b> $article<b>$giorno $mese $anno</b>. | Visualizzata: $seen\n\n";
        }
        if(count($notes) == 1) $end = "nota disciplinare";
        else $end = "note disciplinari";
        $notesText .= "In totale, hai <b>".count($notes)."</b> ".$end.".";
    }
    cb_reply($cbid, $cbtext, false, $cbmid, "<b>\xf0\x9f\x98\x95 Note disciplinari</b>\n\n".$notesText, $tastiera);
}

if($cbdata == "Profile"){
    $tastiera[] = array(
        array(
            "text" => "\xf0\x9f\x91\x8b\xf0\x9f\x8f\xbb Logout",
            "callback_data" => "Logout"
        )
    );
    $tastiera[] = array(
        array(
            "text" => "\xf0\x9f\x94\x99 Torna indietro",
            "callback_data" => "Panel"
        )
    );
    $idArgo = $user->prgAlunno;
    $authToken = $user->authToken;
    $idTelegram = $userID;
    $codiceScuola = $user->codMin;
    $scuola = $user->desSede;
    cb_reply($cbid, $cbtext, false, $cbmid, "\xf0\x9f\x91\xa4 <b>Profilo</b>\n\n\xf0\x9f\x86\x94 <b>Tuo ID di Argo:</b> $idArgo\n\xf0\x9f\x94\x91 <b>Token univoco:</b> <code>$authToken</code>\n\xf0\x9f\x9a\xb9 <b>ID Telegram:</b> $idTelegram\n\n\xf0\x9f\x94\x97 <b>Codice scuola:</b> $codiceScuola\n\xf0\x9f\x94\x96 <b>Nominativo scuola:</b> $scuola", $tastiera);
}

if($cbdata == "GradesList"){
    $gradesText = "";
    $gradesRaw = $user->votiGiornalieri();
    if(count($gradesRaw) == 0){
        $gradesRaw .= "Per ora, non ci sono voti.";
    }
    else{
        $subjectsVarious = array();
        foreach($gradesRaw as $grade){
            array_push($subjectsVarious, $grade['desMateria']);
        }
        $subjects = array_unique($subjectsVarious);
        sort($subjects);
        $i = 0;
        foreach($subjects as $subject){
            ++$i;
            $kb[] = array(
                array(
                    "text" => " ".$subject,
                    "callback_data" => "G|".md5($subject)
                )
            );
        }
        $gradesText .= "Scegli di quale materia vuoi vedere i voti.\n\n<i>Al momento hai ".count($gradesRaw)." voti.</i>";
    }
    $kb[] = array(
        array(
            "text" => "\xf0\x9f\x94\x99 Torna indietro",
            "callback_data" => "Panel"
        )
    );
    cb_reply($cbid, $cbtext, false, $cbmid, "\xf0\x9f\x96\x8a <b>Voti</b>\n$gradesText", $kb);
}

if(explode('|', $cbdata)[0] == "G"){
    $tastiera[] = array(
        array(
            "text" => "\xf0\x9f\x94\x84 Aggiorna",
            "callback_data" => $cbdata
        )
    );
    $tastiera[] = array(
        array(
            "text" => "\xf0\x9f\x94\x99 Torna indietro",
            "callback_data" => "GradesList"
        )
    );
    $subject = explode('|', $cbdata)[1];
    $gradesRaw = $user->votiGiornalieri();
    $gradesSubject = array();
    foreach($gradesRaw as $grade){
        if(md5($grade['desMateria']) == $subject){
            $subjectText = $grade['desMateria'];
            array_push($gradesSubject, $grade);
        }
    }
    $gradesText = "\xf0\x9f\x96\x8a <b>Voti per la materia $subjectText </b>\n\n";
    $grades = array();
    foreach($gradesSubject as $gradeSubject){
        $value = $gradeSubject['codVoto'];
        $type = $gradeSubject['codVotoPratico'];
        array_push($grades, $gradeSubject['decValore']);
        if($gradeSubject['decValore'] >= 6) $result = "\xf0\x9f\x94\xb9";
        else $result = "\xf0\x9f\x94\xbb";
        if($type == "P") $type = "pratico";
        if($type == "S") $type = "scritto";
        else $type = "orale";
        $data = explode('-', $gradeSubject['datGiorno']);
        $giorno = $data[2];
        $mese = getMonth($data[1]);
        $anno = $data[0];
        if($giorno == 1 || $giorno == 11) $article = "l'";
        else $article = "il ";

        if($gradeSubject['desProva'] != "") $desc = "'".$gradeSubject['desProva']."'";
        else $desc = "Nessuna descrizione";

        $gradesText .= "- Voto $type: <b>$value</b> $result- $desc\nAssegnato $article<b>$giorno $mese $anno</b>.\n";
    }
    $total = array_sum($grades);
    $media = intval($total)/count($grades);
    if(count($grades) == 1) $word = "voto";
    else $word = "voti";
    $gradesText .= "\n\xf0\x9f\x94\xb9: Voto <b>sufficiente</b>\n\xf0\x9f\x94\xbb: Voto <b>insufficiente</b>\nLa tua media per questa materia è <b>$media</b>.\nIn totale, hai <b>".count($grades)."</b> $word.";
    cb_reply($cbid, $cbtext, false, $cbmid, $gradesText, $tastiera);
}

if($cbdata == "Today"){
    $gradesText = "";
    $todayRaw = $user->oggiScuola();
    $argomentiText = "";
    $compitiText = "";
    $votiText = "";
    $promemoriaText = "";
    if (count($todayRaw)==0) {
        $todayText = "<i>Nessuna informazione da visualizzare.</i>";
    }
    else {
        foreach ($todayRaw AS $thisEvento) {
            if ($thisEvento['tipo']=="ARG") {
                $argomentiText .= "<i>".$thisEvento['dati']['desMateria']."</i>: ".$thisEvento['dati']['desArgomento']."\n";
            }
            elseif ($thisEvento['tipo']=="COM") {
                $compitiText .= "<i>".$thisEvento['dati']['desMateria']."</i>: ".$thisEvento['dati']['desCompiti']."\n";
            }
            elseif ($thisEvento['tipo']=="VOT") {
                $votiText .= "<i>".$thisEvento['dati']['desMateria']."</i>: <b>".$thisEvento['dati']['codVoto']."</b>\n";
            }
            elseif ($thisEvento['tipo']=="PRO") {
                $promemoriaText .= "<i>".$thisEvento['dati']['desMittente']."</i>: ".$thisEvento['dati']['desAnnotazioni']."\n";
            }
        }
        if (!empty($argomentiText)) {
            $todayText .= "\xF0\x9F\x93\x92 <b>Argomenti</b>\n".$argomentiText."\n";;
        }
        if (!empty($compitiText)) {
            $todayText .= "\xf0\x9f\x93\x9a <b>Compiti</b>\n".$compitiText."\n";
        }
        if (!empty($votiText)) {
            $todayText .= "\xf0\x9f\x96\x8a <b>Voti</b>\n".$votiText."\n";
        }
        if (!empty($promemoriaText)) {
            $todayText .= "\xf0\x9f\x93\x9d <b>Promemoria</b>\n".$promemoriaText."\n";
        }
    }
    $kb = array();
    $kb[] = array(
        array(
            "text" => "\xf0\x9f\x94\x99 Torna indietro",
            "callback_data" => "Panel"
        )
    );
    cb_reply($cbid, $cbtext, false, $cbmid, "\xf0\x9f\x93\x85 <b>Sommario di oggi</b>\n\n$todayText", $kb);
}

/*if($msg == "/panel"){
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
            "callback_data" => "Grades"
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
            "text" => "\xe2\x9a\x99\xef\xb8\x8f Impostazioni",
            "callback_data" => "Settings"
        )
    );
    sm($chatID, "\xf0\x9f\x93\x98 <b>Benvenuto nel pannello!</b>\nScegli cosa vuoi fare.\n\n\xe2\x84\xb9\xef\xb8\x8f <i>Hai effettuato il login, se vuoi disconnetterti clicca su \"Impostazioni\", e poi su \"Disconnettiti\".</i>", $panel);

}*/
