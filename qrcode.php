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
        $this->yellow->system->setDefault("qrcodeShortLinkLength", "30");
        $this->yellow->system->setDefault("qrcodeCache", "qrcodes/");
    }

    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="qrcode" && ($type=="block")) {
            list($content, $label, $style, $size) = $this->yellow->toolbox->getTextArguments($text);
            $content = stripcslashes($content);
            $content = preg_replace('/\R/', "\n", $content);
            if (empty($label)) {
                $qrTypes = [
                    "https://"=>"Url",
                    "http://"=>"Url",
                    "mailto:"=>"Email",
                    "begin:vcard\n"=>"Card",
                    "begin:vevent\n"=>"Event",
                    "geo:"=>"Geo",
                    "tel:"=>"Call",
                    "SMSTO:"=>"Sms",
                    "WIFI:"=>"Wifi",
                ];
                foreach ($qrTypes as $qrType=>$value) {
                    $modifier = preg_match('/[a-z]/', $qrType) ? "i" : "";
                    if (preg_match('@^'.$qrType.'@'.$modifier, $content)) {
                        $label = $this->yellow->language->getTextHtml("qrcodeLabel".$value);
                        break;
                    }
                }
            }
            $link = $shortLink = null;
            $color = $this->yellow->system->get("qrcodeColor");
            $background = $this->yellow->system->get("qrcodeBackground");
            $fileName = $this->yellow->system->get("qrcodeCache").md5(rawurlencode($content)."@".$color."@".$background);
            $location = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreMediaLocation").$fileName;
            $path = $this->yellow->lookup->findMediaDirectory("coreMediaLocation").$fileName;
            if (preg_match('@^(http://|https://|mailto:|geo:|tel:)(.+)@i', $content, $matches)) {
                $link = $content;
                $maxLength = $this->yellow->system->get("qrcodeShortLinkLength");
                $shortLink = mb_strlen($matches[2])<=$maxLength ? $matches[2] : mb_substr($matches[2], 0, $maxLength-1)."…";
            } elseif (preg_match('@^begin:(vcard|vevent)$@mi', $content, $matches)) {
                $contentExtension = strtolower($matches[1])=="vcard" ? "vcf" : "ical";
                $content = preg_replace('/\R/', "\r\n", $content)."\r\n";
                if (!file_exists($path.".".$contentExtension)) {
                    $this->yellow->toolbox->createFile($path.".".$contentExtension, $content, true);
                }
                $link = $location.".".$contentExtension;
                if (preg_match('@^(fn|summary):(.+)@mi', $content, $matches)) {
                    $shortLink = $matches[2];
                }
            } elseif (preg_match('@^SMSTO:(.+?):(.+)$@', $content, $matches)) {
                $shortLink = $matches[1];
                $link = $matches[2];
            } elseif (preg_match('@^WIFI:T:.+?;S:(.+?);P:(.+?);@', $content, $matches)) {
                // TODO: colon and semicolon can be escaped
                $shortLink = $matches[1];
                $link = $matches[2];
            }
            $formattedLabel = $label;
            if (preg_match('@^(.*)\[(.+)\](.*)$@', $formattedLabel, $matches)) {
                $formattedLabel = $matches[1]."<a href=\"".htmlspecialchars($link)."\">".$matches[2]."</a>".$matches[3];
            }
            $formattedLabel = str_replace([ "@shortlink", "@link" ], [ htmlspecialchars($shortLink), htmlspecialchars($link) ], $formattedLabel);
            if (empty($size)) $size = $this->yellow->system->get("qrcodeSize");
            if (!file_exists($path.".png")) {
                $qrcodeImage = file_get_contents("http://api.qrserver.com/v1/create-qr-code/?color=".rawurlencode($color)."&bgcolor=".rawurlencode($background)."&data=".rawurlencode($content)."&qzone=2&margin=0&size=500x500&ecc=L&format=png");
                if (substr($qrcodeImage, 0, 8)=="\211PNG\r\n\032\n") $this->yellow->toolbox->createFile($path.".png", $qrcodeImage, true);
            }
            $output .= "<figure class=\"qrcode";
            if (!empty($style)) $output .= " ".htmlspecialchars($style);
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
