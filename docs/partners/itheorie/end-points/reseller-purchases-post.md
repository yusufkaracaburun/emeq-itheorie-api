# Create a new purchase for a specific reseller
## Request

```http
POST /{reseller}/purchases
```

### Parameters
* `reseller` - `string` - ULID, linking code or chamber of commerce number of the <dfn>reseller</dfn>

## Request
### Schema
| name                        | type              | description                                                                                                                                                                                                                                                                                  |
|-----------------------------|-------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `course`                    | `string`\|`null`  | ULID id off the course you want to activate. Can be omitted if you want to let the student choose which course they want to follow when they first log in.                                                                                                                                   |
| `name`                      | `string`\|`null`  | Name of the student. _**optional unless** `permissions_to_share_progress` is set to `true`. Then we need a name to display on the driving school and students page of https://itheorie.nl._                                                                                                  |
| `email`                     | `string`\|`null`  | Email address of the student. _This is only used for notifications of important changes (for example a theory lesson that is cancelled)._                                                                                                                                                    |
| `mobilePhone`               | `string`\|`null`  | Mobile phone number of the student (preferably international format `+31612345678` otherwise it will be automatically converted (for `NL`) which does not always work well). _This is only used for SMS notifications of important changes (for example a theory lesson that is cancelled)._ |
| `permissionToShareProgress` | `boolean`\|`null` | Yes/no of the <dfn>reseller</dfn> has received permission from the student to view the progress (default `false`). Without this permission, the <dfn>reseller</dfn> cannot view the `/students/{accessCode}` routes where the progress can be seen.                                          |

### Example
```json
{
    "course": "01GYFBWMYGGARXBN40X7FFDCNZ",
    "name": "Voorbeeld Cursist",
    "email": "cursist@voorbeeld.nl",
    "mobilePhone": "+31612345678",
    "permissionToShareProgress": true
}
```

## Response
### `303` See Other
On **success**, you will be redirected to the purchase details page [:link: `/{reseller}/purchases/{purchase}`](reseller-purchases-purchase-get.md).

### Errors

#### Reseller parameter
* `400010` `invalid_reseller_parameter` Reseller parameter is expected to be a ULID or chamber of commerce number, if the value matched neither of the expected formats this message is shown.
* `403004` `reseller_company_is_disabled` The reseller you are using for the request has been disabled at our side, therefor he is not allowed to do anything.
* `403005` `reseller_is_disabled` The reseller you are using for the request has been disabled at our side, therefor he is not allowed to do anything.
* `404001` `reseller_company_not_found_by_id` Reseller id is invalid/missing from our database (should only be invalid, we have not deleted old companies to date).
* `404002` `reseller_company_not_found_by_chamber_of_commerce` No company with the same chamber of commerce number was found in our database. Either registration or changes to the chamber of commerce number are required.
* `404009` `reseller_company_not_found_by_linking_code` Linking code is invalid for any existing company in our database.
* `404003` `reseller_not_found` The reseller has not enabled permission for third party (broker) purchases. The reseller can do this in the driving school section of itheorie.nl.

#### Prerequisites failed
* `400021` `reseller_user_missing_email_contact_method` We require the reseller to have an email address in our system used for invoices/direct debit notifications.
* `400020` `reseller_company_missing_direct_debit_payment_method` We require the reseller to have payment information in our system (account holder/IBAN) for direct debit payments.

#### Request Content
* `400001` `unable_to_decode_request_body` "Something" went wrong tying to parse the request body, not sure what.
* `400002` `unable_to_parse_request_body` Same as `unable_to_decode_request_body` except we can have detailed information (if available by parser) in the response about where the parse error occurred.
```json 
{
    "status": 400,
    "code": 400002,
    "message": "Fout bij het parsen van de verzoek inhoud.",
    "description": "Parse error on line 4:\n...    \"mobilePhone\": \"+316\r}\n---------------------^\nInvalid string, it appears you forgot to terminate a string, or attempted to write a multiline string which is invalid"
}
```

* `400003` `validation_failed` Validation failed on the data sent in the request body, see the response data for more details.
```json
{
    "status": 400,
    "code": 400003,
    "message": "Incorrecte gegevens ingevoerd.",
    "violations": [
        {
            "code": "1a1560ed-f121-434c-83a9-202f90f684ab",
            "message": "De geselecteerde cursus is ongeldig of niet meer beschikbaar.",
            "propertyPath": "course"
        }
    ]
}
```
