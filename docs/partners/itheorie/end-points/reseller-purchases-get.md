# List with all purchases made by the reseller.

## Request
```http
GET /{reseller}/purchases
```

### Parameters
* `reseller` - `string` - ULID, linking code or chamber of commerce number of the <dfn>reseller</dfn>

#### Query Parameters
* `page` - `int` - Page number to return. Default is `1`.
* `limit` - `int` - Maximum number of items to return. Default is `10`.

### Filters
WIP

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
| `data` | `PurchaseData[]` | List with all purchases made by the reseller. |

### Example
```json
{
    "links": {
        "first": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/purchases?page=1",
        "previous": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/purchases?page=3",
        "self": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/purchases?page=4",
        "last": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/purchases?page=4"
    },
    "data": [
        {
            "id": "01H90Z030C2X1KXS06EPPSYQ7E",
            "invoice": 12353,
            "createdAt": "2023-08-29T17:36:05+02:00",
            "price": {
                "amount": "16.45",
                "currency": "EUR"
            },
            "subscription": "01H90Z0320W4JZ5BPWFV13MQEF",
            "accessCode": "e9fdfd45e2a39ad6",
            "expiresAt": "2026-08-01T00:00:00+02:00",
            "loginUrl": "https://test.itheorie.nl/login",
            "directLoginUrl": "https://test.itheorie.nl/qr?code=e9fdfd45e2a39ad6",
            "name": "Pietje Puk",
            "email": "pietje.puk@voorbeeld.nl",
            "mobilePhoneNumber": "+31612345678"
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
