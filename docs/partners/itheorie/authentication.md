# Authentication

## Obtaining a Bearer Token
Basic authentication is used to obtain a bearer token.
Authentication for this request is done using the brokers lens id/password!

### Request
```http
GET /auth
Authorization: Basic aGE6aGE=
```

### Response
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2OTMzMTYzMDIsImlzcyI6Iml0aGVvcmllIiwic3ViIjoiY29ubmVjdC1hcGkiLCJhdWQiOiJCcm9rZXIgVm9vcmJlZWxkIiwicGsiOiI5Zjhka0dubVFXbmZqRkVua3FwczM4azJDMTdjZDI0NWI1OWU0YTcwYjQ1Y2JmMWJhYmQwYzk0MTMzODVjM2Q2ODlhMzZjY2E2OThlOTY1ODczMzM5YyIsInVzZXIiOiIwMUg5MFFaOUQ5WVNOOVo5MFRXQzZSVEZCRSIsImNvbXBhbnkiOiIwMUg5MFBXRVRINDdaVEdKTVA2OTgxUE5KUSIsImFjY291bnQiOiIwMUg5MFE2MzJBNDVFWkpCTldDMURHUFdWUCJ9.nUoDm1rk7Jwg8HL--PC38J_R5nGnx0FyUUrySarfHek"
}
```

### Errors
* `401001` `basic_authentication_required` The requests expects `Authorization` header with `Basic` to be present, see authentication document for more details.

#### Credentials
* `401010` `broker_user_not_found` The user account could not be found, pleases check your LENS ID login credentials.
* `401011` `broker_company_not_found` The user account trying to login is not associated with a company in our database.
* `401012` `broker_account_not_found` The broker account could not be found, you must request access before you can use this api.
* `401003` `invalid_credentials` Authorization Basic request has an invalid password.

#### Account status
* `403001` `broker_user_is_disabled` Your account has been disabled on our side, contact helpdesk@itheorie.nl if you have questions.
* `403002` `broker_company_is_disabled` Your account has been disabled on our side, contact helpdesk@itheorie.nl if you have questions.
* `403003` `broker_account_is_disabled` Your account has been disabled on our side, contact helpdesk@itheorie.nl if you have questions.

#### Other
* `500002` `unknown_authentication_error` Unknown error during authentication, contact us with the details of what you tried so we can fix this!

## Using the Bearer Token for all other requests
For all other requests a bearer token is required.
```http
GET https://itheorie.nl/api/connect
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2OTMzMTYzMDIsImlzcyI6Iml0aGVvcmllIiwic3ViIjoiY29ubmVjdC1hcGkiLCJhdWQiOiJCcm9rZXIgVm9vcmJlZWxkIiwicGsiOiI5Zjhka0dubVFXbmZqRkVua3FwczM4azJDMTdjZDI0NWI1OWU0YTcwYjQ1Y2JmMWJhYmQwYzk0MTMzODVjM2Q2ODlhMzZjY2E2OThlOTY1ODczMzM5YyIsInVzZXIiOiIwMUg5MFFaOUQ5WVNOOVo5MFRXQzZSVEZCRSIsImNvbXBhbnkiOiIwMUg5MFBXRVRINDdaVEdKTVA2OTgxUE5KUSIsImFjY291bnQiOiIwMUg5MFE2MzJBNDVFWkpCTldDMURHUFdWUCJ9.nUoDm1rk7Jwg8HL--PC38J_R5nGnx0FyUUrySarfHek
```
```json
{
    "message": "Welcome to our iTheorie Connect API ..."
}
```

### Errors
:warning: **NOTE** As the bearer token is used for all requests, these broker side related errors **can occur** on any request and will not be mentioned any further.

* `401002` `bearer_token_authentication_required` The request expects `Authorization` header with `Bearer <token>` to be supplied, see authentication document for more details.

#### Token status
* `401005` `token_decoding_error` Error decoding token.
* `401006` `invalid_signature` Invalid token signature.
* `401007` `token_not_yet_valid` _not used yet_
* `401008` `token_has_expired` _not used yet_
* `401009` `token_is_invalid` Token is invalid.
* `401004` `token_is_revoked` Happens to the older tokens when you generate a new one. There is no other form of expiration yet.

#### Account status
* `401010` `broker_user_not_found` The user account could not be found, pleases check your LENS ID login credentials.
* `401011` `broker_company_not_found` The user account trying to login is not associated with a company in our database.
* `401012` `broker_account_not_found` The broker account could not be found, you must request access before you can use this api.
* `403001` `broker_user_is_disabled`
* `403002` `broker_company_is_disabled`
* `403003` `broker_account_is_disabled`

_When disabled: Your account has been disabled on our side, contact helpdesk@itheorie.nl if you have questions._

#### Other
* `500002` `unknown_authentication_error` Unknown error during authentication, contact us with the details of what you tried so we can fix this!

## Continue reading
A brief explanation of our error structure can be found in [error codes](error-codes.md).
If you want to get to the good part you can also continue reading about our [end points](end-points.md) these will reference back to the errors when applicable.
