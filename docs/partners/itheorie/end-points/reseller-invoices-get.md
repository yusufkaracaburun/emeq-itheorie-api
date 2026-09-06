# Lijst met alle facturen voor een specifieke reseller.

## Request
```http
GET /{reseller}/invoices
```

### Parameters
* `reseller` - `string` - ULID, linking code or chamber of commerce number of the <dfn>reseller</dfn>

#### Query Parameters
* `page` - `int` - Page number to return. Default is `1`.
* `limit` - `int` - Maximum number of items to return. Default is `10`.

## Response
### Schema

#### Collection
| name             | type             | description                              |
|------------------|------------------|------------------------------------------|
| `links.first`    | `string`         | URL the to first page.                   |
| `links.previous` | `string`\|`null` | URL to the previous page (if available). |
| `links.self`     | `string`         | URL to the current page                  |
| `links.next`     | `string`\|`null` | URL to the next page (if available).     |
| `links.last`     | `string`         | URL to the last page.                    |
| `data` | `InvoiceData[]` | List with all purchases made by the reseller. |

#### InvoiceData
| name                | type       | description                                                                                                                                                                                                                          |
|---------------------|------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `id`                | `int`      | invoice number, without the `IT` prefix                                                                                                                                                                                              |
| `createdAt`         | `DateTime` | timestamp for when the invoice was created                                                                                                                                                                                           |
| `amountOfPurchases` | `int`      | count for the amount of access codes that were collected for this invoice <br> :warning: **NOTE** _this list returns all invoices for the reseller, if this amount is 0 the invoice was created for a regular order and not for API purchases_ |
| `total`             | `Money`    | invoice total without vat                                                                                                                                                                                                            |
| `vat`               | `array`    | array vat values by percentage (key) and value (value)                                                                                                                                                                               |
| `totalWithVat`      | `Money`    | invoice total including vat                                                                                                                                                                                                          |

#### Money
| name       | type     | description                                     |
|------------|----------|-------------------------------------------------|
| `amount`   | `string` | Decimal value eg. `16.40`                       |
| `currency` | `string` | Used currency currently only `EUR` is possible. |

#### Voorbeeld
```json
{
    "links": {
        "first": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/invoices?page=1",
        "self": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/invoices?page=1",
        "last": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/invoices?page=1"
    },
    "data": [
        {
            "id": 12353,
            "createdAt": "2023-09-01T17:13:45+02:00",
            "amountOfPurchases": 10,
            "total": {
                "amount": "79.62",
                "currency": "EUR"
            },
            "vat": {
                "0.21": "13.82"
            },
            "totalWithVat": {
                "amount": "93.44",
                "currency": "EUR"
            }
        }
    ]
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
