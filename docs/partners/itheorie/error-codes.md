# Error codes
Error codes are prefixed with the HTTP status code and are returned in the response body as an error object with a unique error code, name and description.

These have the same properties for almost everything. One exception is validation errors (`validation_failed`), there are more fields there indicating errors for each field.

## Voorbeeld
```json
{
    "status": 418,
    "code": 418001,
    "message": "im_a_teapot",
    "description": "I'm a teapot"
}
```

:information_source: it can be that on https://test.itheorie.nl more properties are returned (depending on the set environment). These are for debugging purposes only and should not appear in production. Also note that this can contain sensitive information and should be treated as such.

## Continue reading
It is advised to continue reading the [end points](end-points.md) documentation now and reference the summary below when you need to. Most errors are included on the specific end points documentation pages too.

## Errors
### `400` Bad Request
#### `400001` unable_to_decode_request_body
> Error decoding request content.

"Something" went wrong tying to parse the request body, not sure what.

#### `400002` unable_to_parse_request_body
> Error parsing request content.

Same as `unable_to_decode_request_body` except we can have detailed information (if available by parser) in the response about where the parse error occurred.

```json 
{
    "status": 400,
    "code": 400002,
    "message": "Fout bij het parsen van de verzoek inhoud.",
    "description": "Parse error on line 4:\n...    \"mobilePhone\": \"+316\r}\n---------------------^\nInvalid string, it appears you forgot to terminate a string, or attempted to write a multiline string which is invalid"
}
```

#### `400003` validation_failed
> Incorrect data entered.

Validation failed on the data sent in the request body, see the response data for more details.

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

#### `400010` invalid_reseller_parameter
> Invalid reseller parameter.

Reseller parameter is expected to be a ULID or chamber of commerce number, if the value matched neither of the expected formats this message is shown.

#### `400011` invalid_subscription_parameter
> Invalid subscription parameter.

Subscription parameter is expected to a ULID or chamber of commerce number, if the value matched neither of the expected formats this message is shown.

#### `400012` invalid_access_code_parameter
> Invalid access code parameter.

The subscription parameter matches an acces code, but the access code is not valid (could be a typo, or simply wrong).

#### `400020` reseller_company_missing_direct_debit_payment_method
> No direct debit details are known at iTheorie.

We require the reseller to have payment information in our system (account holder/IBAN) for direct debit payments.

#### `400021` reseller_user_missing_email_contact_method
> No email address is known at iTheorie.

We require the reseller to have an email address in our system used for invoices/direct debit notifications.

### `401` Unauthorized
#### `401001` basic_authentication_required
> Authentication (basic) required.

The requests expects `Authorization` header with `Basic` to be present, see authentication document for more details.

#### `401002` bearer_token_authentication_required
> Authentication (bearer token) required.

The request expects `Authorization` header with `Bearer <token>` to be supplied, see authentication document for more details.

#### `401003` invalid_credentials
> Invalid credentials.

Authorization Basic request has an invalid password.

#### `401004` token_is_revoked
> Token is revoked.

Happens to the older tokens when you generate a new one. There is no other form of expiration yet.

#### `401005` token_decoding_error
> Error decoding token.



#### `401006` invalid_signature
> Invalid token signature.



#### `401007` token_not_yet_valid
> Token is not yet valid.

_not used yet_

#### `401008` token_has_expired
> Token has expired.

_not used yet_

#### `401009` token_is_invalid
> Token is invalid.



#### `401010` broker_user_not_found
> Broker not found (user level).

The user account could not be found, pleases check your LENS ID login credentials.

#### `401011` broker_company_not_found
> Broker not found (company level).

The user account trying to login is not associated with a company in our database.

#### `401012` broker_account_not_found
> Broker not found.

The broker account could not be found, you must request access before you can use this api.

### `403` Forbidden
#### `403001` broker_user_is_disabled
> Broker is disabled (user level).

Your account has been disabled on our side, contact helpdesk@itheorie.nl if you have questions.

#### `403002` broker_company_is_disabled
> Broker is disabled (company level).

Your account has been disabled on our side, contact helpdesk@itheorie.nl if you have questions.

#### `403003` broker_account_is_disabled
> Broker is disabled.

Your account has been disabled on our side, contact helpdesk@itheorie.nl if you have questions.

#### `403004` reseller_company_is_disabled
> Your account is disabled at iTheorie, contact us at helpdesk@itheorie.nl for questions.

The reseller you are using for the request has been disabled at our side, therefor he is not allowed to do anything.

#### `403005` reseller_is_disabled
> Your account is disabled at iTheorie, contact us at helpdesk@itheorie.nl for questions.

The reseller you are using for the request has been disabled at our side, therefor he is not allowed to do anything.

#### `403006` reseller_can_not_view_subscription
> You do not have permission from the student to view their progress.

The reseller is not allowed to see the progression of the student, it must first be permitted by the student in the study section of iTheorie or using `permissionToShareProgress` in the [:link: `POST /{reseller}/purchases`](reseller-purchases-post.md) request.

### `404` Not Found
#### `404001` reseller_company_not_found_by_id
> Data could not be found at iTheorie.

Reseller id is invalid/missing from our database (should only be invalid, we have not deleted old companies to date).

#### `404002` reseller_company_not_found_by_chamber_of_commerce
> Can not find the Chamber of Commerce number at iTheorie, make sure the Chamber of Commerce number matches the data on iTheorie. Are you not yet a customer of iTheorie? Then you can register for free at {registerUrl}. After registration, do not forget to give permission for purchases via third parties in the driving school section! Need help? Contact us at helpdesk@itheorie.nl.

Linking code is invalid for any existing company in our database.

#### `404003` reseller_not_found
> No permission has been given for purchases via third parties. Go to the driving school section of iTheorie to give permission. Need help? Contact us at helpdesk@itheorie.nl.

The reseller has not enabled permission for third party (broker) purchases. The reseller can do this in the driving school section of itheorie.nl.

#### `404004` course_not_found
> Course could not be found.



#### `404005` subscription_not_found_by_access_code
> Student could not be found at iTheorie, do you have the correct access code?



#### `404006` subscription_not_found_by_id
> Student could not be found at iTheorie.



#### `404007` invoice_not_found
> Invoice could not be found.



#### `404008` purchase_not_found
> Purchase could not be found.



#### `404009` reseller_company_not_found_by_linking_code
> Data could not be found at iTheorie.

Linking code is invalid for any existing company in our database.

### `500` Internal Server Error
If you get any of these responses, or if you get a 500 response that does not even match our  format, please contact us. We will try to fix it as soon as possible!

#### `500001` unknown_error
> Unknown error, contact us with the details of what you tried so we can fix this!



#### `500002` unknown_authentication_error
> Unknown error during authentication, contact us with the details of what you tried so we can fix this!



### `503` Service Unavailable
It is possible that we are updating iTheorie, usually that is done within a few seconds and no one notices. But sometimes we have to reserve some time for running a migration. At that time it could happen that the API is temporarily unavailable. A response with this status code will be returned. This response might be of any content-type, no error object will be returned.

#### `0` connect_api_exception
_If you see this message please contact us, this is the base class message and should have been overridden._
