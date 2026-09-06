# List of available courses
List with all courses that are **currently available** in iTheorie. 

> :information_source: It is possible that a student is following an older course that is **not in this list**!
>
> E.g. the motorcycle and moped courses have been changed to a new format since the release of iTheorie. Students who were already working on this still have the old courses that are **not in this list**.
>
> If you are looking for data from a course that is no longer active, you can request the specific course with [:link: `GET /{reseller}/courses/{course}`](reseller-courses-course-get.md). This end-point
works with all courses that we have in our system, active or not.

## Request
```http
GET /{reseller}/courses
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
| `data` | `CourseData[]` | List with all purchases made by the reseller. |

#### CourseData
| name            | type       | description                                                                                                                                                            |
|-----------------|------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `id`            | `Ulid`     | course identifier                                                                                                                                                      |
| `license`       | `string`   | driver's license category (`B`: passenger car, `A`: motorcycle, `AM`: moped)                                                                                           |
| `locale`        | `string`   | language of the course `NL` or `EN`                                                                                                                                    |
| `title`         | `string`   | course title                                                                                                                                                           |
| `description`   | `string`   | course description                                                                                                                                                     |
| `image`         | `string`   | url to course image, does not contain any text or advertisement imagery but is unique to each course to use if you want to create some fancy layout                    |
| `publishedAt`   | `DateTime` | date when the course was published                                                                                                                                     |
| `updatedAt`     | `DateTime` | date when the course was last updated (this does not work properly yet, it only updates when the course object itself changes not any of its child items, aka content) |
| `chapters`      | `string[]` | array of chapter titles in the course, can be used as a quick course summary or just to show the count                                                                 |
| `exams`         | `string[]` | array of exam titles available in the course                                                                                                                           |
| `theoryLessons` | `string[]` | array of theory lessons available in the course                                                                                                                        |
| `offer` | `OfferData`\|`null` | detailed price info for the course                                                                                                                                |

#### OfferData
| name                   | type             | description                                                                               |
|------------------------|------------------|-------------------------------------------------------------------------------------------|
| `originalPrice`        | `Money`          | the original price for 1x iTheorie                                                        |
| `currentPrice`         | `Money`          | the current price for 1x iTheorie                                                         |
| `currentPriceDetails`  | `string`\|`null` | description describing if and why the current price is not the same as the original price |
| `vat`                  | `Percentage`     | vat percentage 0.0-1.0 (currently 0.21) that is used for the purchase                     |
| `suggestedRetailPrice` | `Money`          | the suggested retail price for the course                                                 |

#### Money
| name       | type     | description                                     |
|------------|----------|-------------------------------------------------|
| `amount`   | `string` | Decimal value eg. `16.40`                       |
| `currency` | `string` | Used currency currently only `EUR` is possible. |

### Example
```json
{
    "links": {
        "first": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/courses?page=1",
        "self": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/courses?page=1",
        "last": "https://test.itheorie.nl/api/connect/01H90PZFEDWE3YWZJPD8Z7030P/courses?page=1"
    },
    "data": [
        {
            "id": "01GYFBWMYGGARXBN40X7FFDCNZ",
            "license": "B",
            "locale": "NL",
            "title": "Theoriecursus personenauto",
            "description": "Online theorie leren voor het CBR theorie-examen auto, motor, scooter, snorfiets, bromfiets, speed-pedelec of brommobiel.",
            "image": "https://test.itheorie.nl/images/course/fallback.jpg",
            "publishedAt": "2023-04-20T14:58:06+02:00",
            "updatedAt": "2023-08-29T16:54:01+02:00",
            "chapters": [ "Wetgeving", "Voertuigkennis", ... ],
            "exams": [ "Examen 1", "Examen 2", ... ],
            "theoryLessons": [ "Wetgeving", "Voertuigkennis", ... ],
            "offer": {
                "originalPrice": {
                    "amount": "16.80",
                    "currency": "EUR"
                },
                "currentPrice": {
                    "amount": "16.45",
                    "currency": "EUR"
                },
                "currentPriceDetails": "Korting voorbeeld",
                "vat": "0.21",
                "suggestedRetailPrice": {
                    "amount": "49.00",
                    "currency": "EUR"
                }
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
