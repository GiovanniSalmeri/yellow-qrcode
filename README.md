Qrcode 0.8.20
=============
QR codes.

<p align="center"><img src="qrcode-screenshot.png?raw=true" alt="Screenshot"></p>

## How to add a QR code

Create a `[qrcode]` shortcut.

The following arguments are available, all are optional:
 
`Content` = content of the QR code, wrap into quotes if there are spaces; if omitted, the address of the current page is used  
`Label` = label of the QR code, wrap multiple words into quotes, put the link text, if any, between `||`  
`Style` = style, e.g. `left`, `center`, `right`  
`Size` = QR code width and height in pixel  

`Content`, other than just a URL, can be a structured field in order to specify geographic locations, phone calls, email messages, SMS messages, vCards, iCal events, WiFi passwords:

    #url|http://address
    #geo|longitude,latitude
    #call|telephone-number
    #email|to|subject|body
    #sms|telephone-number|text
    #card|surname;name|telephone-number|email|;;street-address;locality;region;postal-code;country
    #event|summary|location|start-time|end-time
    #wifi|ssid|security|password

## Examples

Adding a QR code, with standard or custom label:

    [qrcode https://datenstrom.se/yellow/]
    [qrcode https://datenstrom.se/yellow/ "Go to the |Yellow website|!"]

Adding a QR code, different styles:

    [qrcode https://datenstrom.se/yellow/ - left]
    [qrcode https://datenstrom.se/yellow/ - center]
    [qrcode https://datenstrom.se/yellow/ - right]

Adding a QR code, different sizes:

    [qrcode https://datenstrom.se/yellow/ - right 100]
    [qrcode https://datenstrom.se/yellow/ - right 200]

Adding different kinds of QR codes:

    [qrcode #url|https://datenstrom.se/yellow/]
    [qrcode #geo|41.85181,12.62127]
    [qrcode #call|+39-06-12345678]
    [qrcode "#email|smith@example.com|Request of help"]
    [qrcode "#sms|+39-06-12345678|Hello there!"]
    [qrcode "#card|Rossi;Giuseppe|+39061234567|rossi@example.com|;;Piazza di Spagna 10;Roma;RM;00187;Italia"]
    [qrcode "#event|Yellow Fest|Lund|20220818T220000Z|20220819T220000Z"]
    [qrcode #wifi|MyWifi|WPA|w65s9s67kshqw]

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`QrcodeSize` (default: `150`) = default width and height in pixels  
`QrcodeColor` (default: `000000`) = RGB color  
`QrcodeBackground` (default: `FFFFFF`) = RGB background color  
`QrcodeCache` (default: `qrcodes/`) = directory where codes are stored  

## Installation

[Download extension](https://github.com/GiovanniSalmeri/yellow-qrcode/archive/master.zip) and copy zip file into your `system/extensions` folder. Right click if you use Safari.

This extension uses [QR Code Generator](https://goqr.me/api/). The service provider [does not store or log QR code contents](https://goqr.me/privacy-safety-security/). "QR Code" is a trademark of Denso Wave Inc.

## Developer

Giovanni Salmeri. [Get help](https://github.com/GiovanniSalmeri/yellow-qrcode/issues).
