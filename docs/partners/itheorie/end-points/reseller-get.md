# Reseller information
Detailed information about a <dfn>reseller</dfn>.

> :information_source: can be used to resolve a <dfn>reseller</dfn> chamber of commerce number to get the ID for long term associations.

> :information_source: billing contains some information regarding if the <dfn>reseller</dfn> can purchase products. These are also triggered when making a purchase however they are triggered separately as actual error responses.

## Request
```http
GET /{reseller}
```
```http
GET https://test.theorie.nl/api/connect/69599068
```
```http
GET https://test.theorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P
```

### Parameters
* `reseller` - `string` - ULID, linking code or chamber of commerce number of the <dfn>reseller</dfn>

## Responses
### Schema
#### ResellerData
| name                    | type                  | description                                        |
|-------------------------|-----------------------|----------------------------------------------------|
| `id`                    | `Ulid`                | reseller (company) id                              |
| `name`                  | `string`              | reseller name                                      |
| `email`                 | `string`\|`null`      | current email address                              |
| `defaultAddress`        | `AddressData`\|`null` | address data for the reseller                      |
| `billing`               | `BillingData`         | billing information                                |

#### AddressData
| name           | type             | description                                     |
|----------------|------------------|-------------------------------------------------|
| `streetName`   | `string`         |                                                 |
| `streetNumber` | `int`            |                                                 |
| `addition`     | `string`\|`null` | house number addition (A, unit B, etc...)       |
| `zipCode`      | `string`         |                                                 |
| `city`         | `string`         |                                                 |
| `country`      | `string`         | string ISO 3166-1 alpha-2 (currently only `NL`) |

#### BillingData
| name                    | type                  | description                                                         |
|-------------------------|-----------------------|---------------------------------------------------------------------|
| `accountHolder`         | `string`\|`null`      | reseller account holder name                                        |
| `iban`                  | `string`\|`null`      | reseller account number (IBAN)                                      |
| `billingAddress`        | `AddressData`\|`null` | reseller billing address falling back to default address if not set |
| `canPurchase`           | `bool`                | whether the reseller can purchase products                          |
| `canNotPurchaseReasons` | `string[]`            | reasons why the reseller can not purchase products                  |

### Example
```json
{
    "id": "01H90PZFEDWE3YWZJPD8Z7030P",
    "name": "Reseller Voorbeeld",
    "email": "reseller@voorbeeld.nl",
    "defaultAddress": {
        "streetName": "Oosterwal",
        "streetNumber": 4,
        "addition": null,
        "zipCode": "7241AP",
        "city": "Lochem",
        "country": "NL"
    },
    "billing": {
        "accountHolder": "Reseller Voorbeeld",
        "iban": "NL96SNSB0115541241",
        "billingAddress": null,
        "canPurchase": false,
        "canNotPurchaseReasons": {
            "400002": "Er zijn geen incasso gegevens bekend bij iTheorie.",
            "400003": "Er is geen e-mailadres bekend bij iTheorie."
        }
    }
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
