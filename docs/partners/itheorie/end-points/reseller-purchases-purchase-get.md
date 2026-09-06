# Purchase details

## Request
```http
GET /{reseller}/purchases/{purchase}
```

### Parameters
* `reseller` - `string` - ULID, linking code or chamber of commerce number of the <dfn>reseller</dfn>
* `purchase` - `string` - ULID of the purchase

## Response

### Schema
#### PurchaseData
| name                | type               | description                                                                                                                                                                                                                                      |
|---------------------|--------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `id`                | `Ulid`             | purchase ID                                                                                                                                                                                                                                      |
| `invoice`           | `int`\|`null`      | invoice number (without `IT` prefix), note that many purchases are collected in an invoice so these can be the same for other purchases                                                                                                          |
| `createdAt`         | `DateTime`         | date when the purchase was made                                                                                                                                                                                                                  |
| `price`             | `Money`            | the price used for this purchase                                                                                                                                                                                                                 |
| `subscription`      | `Ulid`             | Our internal name for the "Access Code ID" this can be used as an alternative when performing requests requiring an access code as parameter (this is recommended for long term storage as access codes might not be the only way in the future) |
| `accessCode`        | `string`           | the access code that was purchased which can be used to login on to itheorie                                                                                                                                                                     |
| `expiresAt`         | `DateTime`\|`null` | a timestamp up to when the access code can be used, access codes can be used 3 years from purchase date by default to avoid them being passed on and on - it is our only piracy protection                                                       |
| `loginUrl`          | `string`           | a URL to the login page on itheorie.nl where the user needs to enter their access code                                                                                                                                                           |
| `directLoginUrl`    | `string`           | a URL to the direct login page on itheorie.nl when a valid code is used a user skips the login screen                                                                                                                                            |
| `name`              | `string`\|`null`   | the students name, used in communication/ as a name for driving schools shared progress list                                                                                                                                                     |
| `email`             | `string`\|`null`   | the students email address, only used for (less) important notifications (or fallback if mobile is missing)                                                                                                                                      |
| `mobilePhoneNumber` | `string`\|`null`   | the students mobile phone number (international format +31612345678), only used for important sms notifications e.g. when a theory lesson is cancelled                                                                                           |

#### Money
| name       | type     | description                                     |
|------------|----------|-------------------------------------------------|
| `amount`   | `string` | Decimal value eg. `16.40`                       |
| `currency` | `string` | Used currency currently only `EUR` is possible. |

### Example
```json
{
    "id": "01H910J9TXYFCT994W4JFN0RD7",
    "invoice": null,
    "createdAt": "2023-08-29T18:03:30+02:00",
    "price": {
        "amount": "16.45",
        "currency": "EUR"
    },
    "subscription": "01H910J9WJVR9GBHY9G43ZRA03",
    "accessCode": "9827c1e3cafe4b2a",
    "expiresAt": "2026-08-01T00:00:00+02:00",
    "loginUrl": "https://test.itheorie.nl/inloggen",
    "directLoginUrl": "https://test.itheorie.nl/qr?code=9827c1e3cafe4b2a",
    "name": "Voorbeeld Cursist",
    "email": "cursist@voorbeeld.nl",
    "mobilePhoneNumber": "+31612345678"
}
```

### Errors

#### Reseller parameter
* `400010` `invalid_reseller_parameter` Reseller parameter is expected to be a ULID or chamber of commerce number, if the value matched neither of the expected formats this message is shown.
* `403004` `reseller_company_is_disabled` The reseller you are using for the request has been disabled at our side, therefor he is not allowed to do anything.
* `403005` `reseller_is_disabled` The reseller you are using for the request has been disabled at our side, therefor he is not allowed to do anything.
* `404001` `reseller_company_not_found_by_id` Reseller id is invalid/missing from our database (should only be invalid, we have not deleted old companies to date).
* `404002` `reseller_company_not_found_by_chamber_of_commerce` No company with the same chamber of commerce number was found in our database. Either registration or changes to the chamber of commerce number are required.
* `404009` `reseller_company_not_found_by_linking_code` Linking code is invalid for any existing company in our database.
* `404003` `reseller_not_found` The reseller has not enabled permission for third party (broker) purchases. The reseller can do this in the driving school section of itheorie.nl.

#### Purchase parameter
* `404008` `purchase_not_found` Purchase could not be found.
