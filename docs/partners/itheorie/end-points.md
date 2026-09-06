# End Points

## Generating API tokens
[:link: `GET /auth`](authentication.md) -
http basic request returning an bearer token to access everything else.

## Resellers
[:link: `GET /{reseller}`](end-points/reseller-get.md) -
get information about the reseller like name, address billing information etc. and if he can actually create a purchase. 

## Cursussen
[:link: `GET /{reseller}/courses`](end-points/reseller-courses-get.md) -
a list of all available courses as this moment.

[:link: `GET /{reseller}/courses/{course}`](end-points/reseller-courses-course-get.md) -
information about any specific course in our system regardless if it is still available or not.

## Purchases
[:link: `GET /{reseller}/purchases`](end-points/reseller-purchases-get.md) - 
a list of all purchases made by the reseller.

[:link: `GET /{reseller}/purchases/{purchase}`](end-points/reseller-purchases-purchase-get.md) - 
details about a specific purchase.

[:link: `POST /{reseller}/purchases`](end-points/reseller-purchases-post.md) - 
creates a new course purchase that will generate an access code for the student.

## Invoices
[:link: `GET /{reseller}/invoices`](end-points/reseller-invoices-get.md) - 
get a list of invoices for the reseller.

[:link: `GET /{reseller}/invoices/{invoice}`](end-points/reseller-invoices-invoice-get.md) -
download the invoice as PDF.

## Student progression
[:link: `GET /{reseller}/students/{subscription}`](end-points/reseller-students-subscription-get.md) -
get basic information about a student and his progression.

[:link: `GET /{reseller}/students/{subscription}/detailed`](end-points/reseller-students-subscription-detailed-get.md) -
get detailed information about a student and his progression.
