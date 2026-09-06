# Connect API
## Curiosities
Before we start. We assume that most technical terms regarding HTTP/REST API/JSON are known, this documentation is not intended for if you have never worked with API's.

Outside of the technical terms there are some extra points of attention used in this documentation that might be unclear:
<dl>
<dt><dfn>reseller</dfn>, rijscholen, verkoper</dt>
<dd>Driving schools, companies, etc. that sell the product to the end user (students)</dd>
<dt><dfn>broker</dfn>, tussenpersoon, wederverkoper, affiliate</dt>
<dd>Driving schools, companies, etc. that use this API in their product to sell products to <dfn>reseller</dfn></dd>
<dt><dfn>legacy api</dfn></dt>
<dd>Refers to our old JSON RPC Connect API, sometimes mentioned for clarification with migrations</dd>
<dt><dfn>kvk</dfn></dt>
<dd>Chamber of Commerce number (8 digits, as a string because sometimes they start with a 0), this is used as an argument in several end points. Note that the chamber of commerce number can be changed, do not use it for associations when storing things locally. It is also possible to work with static IDs in this new API.</dd>
<dt><dfn>toegangscode</dfn>, <dfn>access code</dfn>, <dfn>subscription</dfn></dt>
<dd>Access codes are the login codes that the student uses to log in to itheorie.nl. Subscription is the internal name that our itheorie student/user entity has received, if there is a reference to an id for an access code (instead of the access code itself) then it will be called that way.</dd>
</dl>

## Let's get started
Our API has been given the name Connect API (because it was found on that path). This API is intended for <dfn id="broker">brokers</dfn> who use our services. The ultimate goal of this API is to sell iTheorie access codes automatically via/to external parties.

If you want to get started please contact us so we can provide you with a test account on https://test.itheorie.nl. We would also need an external development IP address(es) to add you to the whitelist. 

Everything is available under the same URL as the legacy api. Root POST calls (`POST /api/connect`) still go to the old system (which will continue to work for the time being). All other calls under `https://test.itheorie.nl/api/connect` will connect to our new API. All paths documented in this documentation are relative to `https://test.itheorie.nl/api/connect` (or the live version `https://itheorie.nl/api/connect`) it is advised to do the same, so they can easily be swapped from test to live.

### JSON
Our api uses JSON. All requests must have the header `Content-Type: application/json` (if they have content) and all responses will have the header `Content-Type: application/json`.

#### Language negotiation
The API supports the `Accept-Language` header for both Dutch `nl` and English `en`. If this header is present, the API will try to return the response in the requested language. If the requested language is not available, or the header is not present, the API will return the response in Dutch. This is mainly useful for translated client side validation error messages.

```http
GET https://test.itheorie.nl/api/connect
Accept-Language: en
```
```json
{
    "message": "Welcome to our iTheorie Connect API ..."
}
```

_If you would like another language to be supported in our API, please contact us, and we'll see what we can do for it. Note that this does not include course languages._

## Default credentials
On rare occasions it is possible that the database gets reset in the test environment. We have a default username/password that will be sent to you privately. It is not advised to change the username and password of your account in the test environment (as they get reset to the initial values). At the same time your generated access token is removed, and it might happen they "suddenly" stop working - a new one needs to be generated.

At the same time a few default resellers are generated for testing purposes. The default reseller have the following credentials:

| ID                           | CoC        | Name                                           | Details                       |
|------------------------------|------------|------------------------------------------------|-------------------------------|
| `01HA9XR6EMNA13CCAXS2J09K85` | `69599084` | Test EMZ Dagobert                              |                               |
| `01HA9YR6WZM6SSQWGZGEX5F0BW` | `68727720` | Test NV Katrien                                | No email and payment method   |
| `01HA9YRB7H7WANKRRRM668C236` | `90004760` | Local Funzoom N.V.                             | No email                      |
| `01HA9YRFN9NS3YYQVVW1JHA46A` | `68750110` | Test BV Donald                                 | Blocked user                  |
| `01HA9YRMDZCQGXZD7CW9XBAR0E` | `90001354` | Grand Kontex B.V.                              | Blocked company               |
| `01HA9YRR5RX411AQ8PWS7MV4X2` | `69599068` | Test Stichting Bolderbast                      | Blocked reseller              |
| `01HA9YRVH4V82660GPKFQG4FZV` | `90000102` | Stichting Free opentrans                       | No address                    |
| `01HA9YRYWZC7AZSXAGKQCXVY15` | `90006623` | Stichting Uc027Tc01Tg01 1652253635399          | API Price agreement (10e p/c) |
| `01HA9YS2D70NBK4ZJ6Z8QZ90ES` | `69599076` | Test VOF Guus                                  |                               |
| `01HF6KXMWHTXMWFDKWS47JZAEQ` | `69599076` | Regional Qvolane - MA                          |                               |
| `01HF6KZYBSDJBWTR2ZVKNZN9KK` | `90005414` | Regional Stimflex Coöperatie                   |                               |
| `01HF6MDH288RWJ2SENDEQ02BXV` | `55344526` | Global Kontex - MA                             |                               |
| `01HF6MDP9EGEHAE8Y3VM4W2Z8M` | `90002520` | Onderlinge Waarborgmaatschappij Local Dongplus |                               |
| `01HF6MDS7CGVCMS8R4PF9Y0533` | `90002490` | Global Conron                                  |                               |
| `01HF6MDWC3D04EG1TG1D035R49` | `90001745` | Global Bigdofind                               |                               |
| `01HF6ME0KA9BEPYM2M57PXVC70` | `90003942` | Free Stathex                                   |                               |

## Continue reading
* [Authentication](authentication.md)
* [End Points](end-points.md)
* [Error codes](error-codes.md)

## Contact
It can be that some things are not clear, you have a question about something or things are not working as expected. In any case please contact us!

For general questions you can contact [Peter Somers](mailto:p.somers@lensmedia.nl). For technical questions/suggestions you can contact our [IT department](mailto:it@lensmedia.nl) or use [GitHub issues](https://github.com/lensmedia/itheorie.nl-public/issues)! And we will try to help you as soon as possible (workdays from 9.00 - 17.00).
