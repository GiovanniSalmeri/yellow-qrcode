Qrcode 0.8.20
=============
QR codes.

<p align="center"><img src="qrcode-screenshot.png?raw=true" width="795" height="836" alt="Screenshot"></p>

## How to add a QR code

Create a `[qrcode]` shortcut, alone in a paragraph.

The following arguments are available, all but the first argument are optional:
 
`Content` = content of the QR code, wrap into quotes if there are spaces  
`Label` = label of the QR code, wrap multiple words into quotes  
`Style` = style, e.g. `left`, `center`, `right`  
`Size` = QR code width and height in pixel  

## Examples

Adding a QR code, with standard or custom label:

    [qrcode https://datenstrom.se/yellow/]
    [qrcode https://datenstrom.se/yellow/ "Go to the [Yellow website]!"]

Adding a QR code, different styles:

    [qrcode https://datenstrom.se/yellow/ - left]
    [qrcode https://datenstrom.se/yellow/ - center]
    [qrcode https://datenstrom.se/yellow/ - right]

Adding a QR code, different sizes:

    [qrcode https://datenstrom.se/yellow/ - right 100]
    [qrcode https://datenstrom.se/yellow/ - right 200]

QR codes can also be used for geographic locations, phone calls, email messages, SMS messages, vCards, iCal events, WiFi identifiers and passwords:

    [qrcode geo:41.85181,12.62127]
    [qrcode tel:+39-06-12345678]
    [qrcode "mailto:smith@example.com?subject=Request of help"]
    [qrcode "SMSTO:+39-06-12345678:Hello there!"]
    [qrcode "BEGIN:VCARD\nFN:John Smith\nEMAIL:smith@example.com\nEND:VCARD"]
    [qrcode "BEGIN:VEVENT\nSUMMARY:Yellow Fest\nDTSTART:20220818T220000Z\nDTEND:20220819T220000Z\nEND:VEVENT"]
    [qrcode WIFI:T:WPA;S:MyWifi;P:w65s9s67kshqw;;]

However, not all QR scanners will properly interpret all these types.

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`QrcodeSize` (default: `150`) = default width and height in pixels  
`QrcodeColor` (default: `000000`) = RGB color  
`QrcodeBackground` (default: `FFFFFF`) = RGB background color  
`QrcodeShortLinkLength` (default: `30`) = maximum number of characters of @shortlink in labels  
`QrcodeCache` (default: `qrcodes/`) = directory where codes are stored  

## Installation

[Download extension](https://github.com/GiovanniSalmeri/yellow-qrcode/archive/master.zip) and copy zip file into your `system/extensions` folder. Right click if you use Safari.

This extension uses [QR Code Generator](https://goqr.me/api/). The service provider [does not store or log QR code contents](https://goqr.me/privacy-safety-security/). "QR Code" is a trademark of Denso Wave Incorporated.

## Developer

Giovanni Salmeri. [Get help](https://github.com/GiovanniSalmeri/yellow-qrcode/issues).
