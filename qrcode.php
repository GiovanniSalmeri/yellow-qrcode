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
        if ($name=="qrcode" && ($type=="block" || $type=="inline")) {
            list($content, $label, $style, $size) = $this->yellow->toolbox->getTextArguments($text);
            if (!strlen($content)) {
                $kind = "url";
                $parts = [ $this->yellow->page->getUrl() ];
            } elseif ($content[0]=="#") {
                $parts = $this->yellow->toolbox->getTextList($content, "|", 5);
                $kind = substr(array_shift($parts), 1);
            } else {
                $kind = "url";
                $parts = [ $content ];
            }
            if (empty($label)) $label = $this->yellow->language->getTextHtml("qrcodeLabel".ucfirst($kind));
            $color = $this->yellow->system->get("qrcodeColor");
            $background = $this->yellow->system->get("qrcodeBackground");
            $fileName = $this->yellow->system->get("qrcodeCache").md5(rawurlencode($content)."@".$color."@".$background);
            $location = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreMediaLocation").$fileName;
            $path = $this->yellow->lookup->findMediaDirectory("coreMediaLocation").$fileName;
            if ($kind=="url" || $kind=="geo" || $kind=="call") {
                if ($kind=="geo") {
                    $link = "geo:".$parts[0];
                } elseif ($kind=="call") {
                    $link = "tel:".$parts[0];
                } else {
                    $link = $parts[0];
                }
                $content = $link;
                $address = preg_replace('@^https?://@', "", $parts[0]);
                $maxLength = $this->yellow->system->get("qrcodeShortLinkLength");
                $shortLink = mb_strlen($address )<=$maxLength ? $address  : mb_substr($address, 0, $maxLength-1)."…";
            } elseif ($kind=="card" || $kind=="event") {
                if ($kind=="card") {
                    $contentExtension = "vcf";
                    $content = "BEGIN:VCARD\r\n";
                    foreach ([ "N", "TEL", "EMAIL", "ADR" ] as $i=>$tag) {
                        if (!empty($parts[$i])) $content .= $tag.":".str_replace([ '\\', ',' ], [ '\\\\', '\,' ], trim($parts[$i]))."\r\n";
                    }
                    $content .= "END:VCARD\r\n";
                    $nameParts = explode(";", $parts[0]);
                    if (count($nameParts)>=2) {
                        $shortLink = trim($nameParts[1])." ".trim($nameParts[0]);
                    } else {
                        $shortLink = trim($nameParts[0]);
                    }
                } else {
                    $contentExtension = "ical";
                    $content = "BEGIN:VEVENT\r\n";
                    foreach ([ "SUMMARY", "LOCATION", "DTSTART", "DTEND" ] as $i=>$tag) {
                        if (!empty($parts[$i])) $content .= $tag.":".str_replace([ '\\', ',' ], [ '\\\\', '\,' ], trim($parts[$i]))."\r\n";
                    }
                    $content .= "END:VEVENT\r\n";
                    $shortLink = $parts[0];
                }
                if (!file_exists($path.".".$contentExtension)) {
                    $this->yellow->toolbox->createFile($path.".".$contentExtension, $content, true);
                }
                $link = $location.".".$contentExtension;
            } elseif ($kind=="sms") {
                $link = $parts[1];
                $shortLink = $parts[0];
                $parts = array_map(function($p) { return str_replace([ '\\', ':', ';' ], [ '\\\\', '\:', '\;' ], $p); }, $parts);
                $content = "SMSTO:".$parts[0].":".$parts[1];
            } elseif ($kind=="wifi") {
                $link = $parts[2];
                $shortLink = $parts[0];
                $parts = array_map(function($p) { return str_replace([ '\\', ':', ';' ], [ '\\\\', '\:', '\;' ], $p); }, $parts);
                $content = "WIFI:T:".$parts[1].";S:".$parts[0].";P:".$parts[2].";;";
            } elseif ($kind=="email") {
                $shortLink = $parts[0];
                $content = "mailto:".rawurlencode($parts[0]);
                if ($parts[1]) {
                    $content .= "?subject=".rawurlencode($parts[1]);
                    if ($parts[2]) {
                        $content .= "&body=".rawurlencode($parts[2]);
                    }
                }
                $link = $content;
            } else {
                $link = $shortLink = null;
            }
            $formattedLabel = $label;
            if (preg_match('@^(.*)\|(\S(?:.*?\S)?)\|(.*)$@', $formattedLabel, $matches)) {
                $formattedLabel = $matches[1]."<a href=\"".htmlspecialchars($link)."\">".$matches[2]."</a>".$matches[3];
            }
            $formattedLabel = str_replace([ "@text", "@link" ], [ htmlspecialchars($shortLink), htmlspecialchars($link) ], $formattedLabel);
            if (empty($size)) $size = $this->yellow->system->get("qrcodeSize");
            if (!file_exists($path.".png")) {
                $qrcodeImage = file_get_contents("https://api.qrserver.com/v1/create-qr-code/?color=".rawurlencode($color)."&bgcolor=".rawurlencode($background)."&data=".rawurlencode($content)."&qzone=2&margin=0&size=500x500&ecc=L&format=png");
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
