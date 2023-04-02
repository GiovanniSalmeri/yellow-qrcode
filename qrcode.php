<?php
// Qrcode extension, https://github.com/GiovanniSalmeri/yellow-qrcode

class YellowQrcode {
    const VERSION = "0.8.20";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("qrcodeSize", "150");
        $this->yellow->system->setDefault("qrcodeColor", "000000");
        $this->yellow->system->setDefault("qrcodeBackground", "FFFFFF");
        $this->yellow->system->setDefault("qrcodeCache", "qrcodes/");
        $this->yellow->language->setDefaults([
            "Language: it",
            "QrcodeLabelUrl: Visita |@text|",
            "QrcodeLabelSelf: Guarda nel telefono",
            "QrcodeLabelCard: Aggiungi alla rubrica |@text|",
            "QrcodeLabelSms: Scrivi a @text «@link»",
            "QrcodeLabelCall: Chiama il |@text|",
            "QrcodeLabelGeo: Visita |@text|",
            "QrcodeLabelEvent: Aggiungi al calendario |@text|",
            "QrcodeLabelEmail: Manda una email a |@text|",
            "QrcodeLabelWifi: Collegati a @text (password: @link)",
            "Language: de",
            "QrcodeLabelUrl: Besuch |@text|",
            "QrcodeLabelSelf: Siehe in deinem Handy",
            "QrcodeLabelCard: Füge zu Kontakten |@text| hinzu",
            "QrcodeLabelSms: Sende die SMS »@link« an @text",
            "QrcodeLabelCall: Ruf |@text| an",
            "QrcodeLabelGeo: Besuch |@text|",
            "QrcodeLabelEvent: Füge zum Kalender |@text| hinzu",
            "QrcodeLabelEmail: Sende eine E-Mail an |@text|",
            "QrcodeLabelWifi: Verbinde mit @text (Passwort: @link)",
            "Language: en",
            "QrcodeLabelUrl: Visit |@text|",
            "QrcodeLabelSelf: See in your phone",
            "QrcodeLabelCard: Add to contacts |@text|",
            "QrcodeLabelSms: Message @text ‘@link’",
            "QrcodeLabelCall: Call |@text|",
            "QrcodeLabelGeo: Visit |@text|",
            "QrcodeLabelEvent: Add to calendar |@text|",
            "QrcodeLabelEmail: Email |@text|",
            "QrcodeLabelWifi: Connect to @text (password: @link)",
            "Language: es",
            "QrcodeLabelUrl: Visitar |@text|",
            "QrcodeLabelSelf: Mirar en el teléfono",
            "QrcodeLabelCard: Añadir a los contactos |@text|",
            "QrcodeLabelSms: Enviar a @text “@link”",
            "QrcodeLabelCall: Llamar al |@text|",
            "QrcodeLabelGeo: Visitar |@text|",
            "QrcodeLabelEvent: Añadir al calendario |@text|",
            "QrcodeLabelEmail: Escribir a |@text|",
            "QrcodeLabelWifi: Conectarse a @text (contraseña: @link)",
            "Language: fr",
            "QrcodeLabelUrl: Visitez |@text|",
            "QrcodeLabelSelf: Regardez dans le téléphone",
            "QrcodeLabelCard: Ajoutez aux contacts |@text|",
            "QrcodeLabelSms: Envoyez a @text le SMS « @link »",
            "QrcodeLabelCall: Appelez le |@text|",
            "QrcodeLabelGeo: Visitez |@text|",
            "QrcodeLabelEvent: Ajoutez au calendrier |@text|",
            "QrcodeLabelEmail: Envoyez un courriel a |@text|",
            "QrcodeLabelWifi: Connectez-vous à @text (mot de passe: @link)",
            "Language: nl",
            "QrcodeLabelUrl: Bezoek |@text|",
            "QrcodeLabelSelf: Kijk in je telefoon",
            "QrcodeLabelCard: Voeg toe aan contacten |@text|",
            "QrcodeLabelSms: Stuur @text de SMS „@link”",
            "QrcodeLabelCall: Bel |@text|",
            "QrcodeLabelGeo: Bezoek |@text|",
            "QrcodeLabelEvent: Voeg toe aan kalender |@text|",
            "QrcodeLabelEmail: Stuur |@text| een email",
            "QrcodeLabelWifi: Maak verbinding met @text (wachtwoord: @link)",
            "Language: pt",
            "QrcodeLabelUrl: Visite |@text|",
            "QrcodeLabelSelf: Veja no telefone",
            "QrcodeLabelCard: Acrescente aos contatos |@text|",
            "QrcodeLabelSms: Envie a @text uma mensagem “@link”",
            "QrcodeLabelCall: Ligue para |@text|",
            "QrcodeLabelGeo: Visite |@text|",
            "QrcodeLabelEvent: Acrescente ao calendário |@text|",
            "QrcodeLabelEmail: Envie um e-mail a |@text|",
            "QrcodeLabelWifi: Conecte-se a @text (senha: @link)",
        ]);
    }

    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="qrcode" && ($type=="block" || $type=="inline")) {
            list($content, $label, $style, $size) = $this->yellow->toolbox->getTextArguments($text);
            if (!strlen($content)) {
                $kind = "self";
                $parts = [ $this->yellow->page->getUrl() ];
            } elseif ($content[0]=="#") {
                $parts = $this->yellow->toolbox->getTextList($content, "|", 5);
                $kind = substr(array_shift($parts), 1);
            } else {
                $kind = "url";
                $parts = [ $content ];
            }
            if (is_string_empty($label)) $label = $this->yellow->language->getTextHtml("qrcodeLabel".ucfirst($kind));
            $color = $this->yellow->system->get("qrcodeColor");
            $background = $this->yellow->system->get("qrcodeBackground");
            $fileName = $this->yellow->system->get("qrcodeCache").md5(rawurlencode($content)."@".$color."@".$background);
            $location = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreMediaLocation").$fileName;
            $path = $this->yellow->lookup->findMediaDirectory("coreMediaLocation").$fileName;
            if ($kind=="url" || $kind=="geo" || $kind=="self" || $kind=="call") {
                if ($kind=="geo") {
                    $link = "geo:".$parts[0];
                } elseif ($kind=="call") {
                    $link = "tel:".$parts[0];
                } else {
                    $link = $parts[0];
                }
                $content = $link;
                $address = preg_replace('@^https?://@i', "", $parts[0]);
                $shortText = $address;
            } elseif ($kind=="card" || $kind=="event") {
                if ($kind=="card") {
                    $contentExtension = "vcf";
                    $id = "VCARD";
                    $tags = [ "N", "TEL", "EMAIL", "ADR" ];
                    $nameParts = explode(";", $parts[0]);
                    if (count($nameParts)>=2) {
                        $shortText = trim($nameParts[1])." ".trim($nameParts[0]);
                    } else {
                        $shortText = trim($nameParts[0]);
                    }
                } else {
                    $contentExtension = "ical";
                    $id = "VEVENT";
                    $tags = [ "SUMMARY", "LOCATION", "DTSTART", "DTEND" ];
                    $shortText = $parts[0];
                }
                $content = "BEGIN:".$id."\r\n";
                foreach ($tags as $i=>$tag) {
                    if (!is_string_empty($parts[$i])) $content .= $tag.":".str_replace([ '\\', ',' ], [ '\\\\', '\,' ], trim($parts[$i]))."\r\n";
                }
                $content .= "END:".$id."\r\n";
                if (!file_exists($path.".".$contentExtension)) {
                    $this->yellow->toolbox->createFile($path.".".$contentExtension, $content, true);
                }
                $link = $location.".".$contentExtension;
            } elseif ($kind=="sms") {
                $link = $parts[1];
                $shortText = $parts[0];
                $parts = array_map(function($p) { return str_replace([ '\\', ':', ';' ], [ '\\\\', '\:', '\;' ], $p); }, $parts);
                $content = "SMSTO:".$parts[0].":".$parts[1];
            } elseif ($kind=="wifi") {
                $link = $parts[2];
                $shortText = $parts[0];
                $parts = array_map(function($p) { return str_replace([ '\\', ':', ';' ], [ '\\\\', '\:', '\;' ], $p); }, $parts);
                $content = "WIFI:T:".$parts[1].";S:".$parts[0].";P:".$parts[2].";;";
            } elseif ($kind=="email") {
                $shortText = $parts[0];
                $content = "mailto:".rawurlencode($parts[0]);
                if ($parts[1]) {
                    $content .= "?subject=".rawurlencode($parts[1]);
                    if ($parts[2]) {
                        $content .= "&body=".rawurlencode($parts[2]);
                    }
                }
                $link = $content;
            } else {
                $link = $shortText = null;
            }
            $formattedLabel = $label;
            if (preg_match('@^(.*)\|(\S(?:.*?\S)?)\|(.*)$@', $formattedLabel, $matches)) {
                $formattedLabel = $matches[1]."<a href=\"".htmlspecialchars($link)."\">".$matches[2]."</a>".$matches[3];
            }
            $formattedLabel = str_replace([ "@text", "@link" ], [ htmlspecialchars($shortText), htmlspecialchars($link) ], $formattedLabel);
            if (is_string_empty($size)) $size = $this->yellow->system->get("qrcodeSize");
            if (!file_exists($path.".png")) {
                $qrcodeImage = file_get_contents("https://api.qrserver.com/v1/create-qr-code/?color=".rawurlencode($color)."&bgcolor=".rawurlencode($background)."&data=".rawurlencode($content)."&qzone=2&margin=0&size=500x500&ecc=L&format=png");
                if (substr($qrcodeImage, 0, 8)=="\211PNG\r\n\032\n") $this->yellow->toolbox->createFile($path.".png", $qrcodeImage, true);
            }
            $output .= "<figure class=\"qrcode";
            if (!is_string_empty($style)) $output .= " ".htmlspecialchars($style);
            $output .= "\">\n";
            $output .= "<img alt=\"QR code\" width=\"".htmlspecialchars($size)."\" height=\"".htmlspecialchars($size)."\" aria-hidden=\"true\" src=\"".$location.".png"."\" />\n";
            $output .= "<figcaption>".$formattedLabel."</figcaption>\n";
            $output .= "</figure >\n";
        }
        return $output;
    }

    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name=="header") {
            $extensionLocation = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreExtensionLocation");
            $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$extensionLocation}qrcode.css\" />\n";
        }
        return $output;
    }
}
