# Cursus gegevens
Specific course information (also works for courses that are no longer active).

_:information_source: active courses are courses that can be purchased right now. It is
also possible that students follow older courses that have been unlisted._

## Request
```http
GET /{reseller}/courses/{course}
```

### Parameters
* `reseller` - `string` - ULID, linking code or chamber of commerce number of the <dfn>reseller</dfn>
* `course` - `string` - ULID of the <dfn>course</dfn>

## Response
#### Schema
As these also include courses that are no longer active, there is no `OfferData` included. Use the courses list to get the active courses and their prices.

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

#### Example
```json
{
    "id": "01GYFBWMYGGARXBN40X7FFDCNZ",
    "license": "B",
    "locale": "NL",
    "title": "Theoriecursus personenauto",
    "description": "Online theorie leren voor het CBR theorie-examen auto, motor, scooter, snorfiets, bromfiets, speed-pedelec of brommobiel.",
    "image": "https://test.itheorie.nl/assets/images/course/fallback.jpg",
    "publishedAt": "2023-04-20T14:58:06+02:00",
    "updatedAt": "2023-08-29T16:54:01+02:00",
    "chapters": [ "Wetgeving", "Voertuigkennis", ... ],
    "exams": [ "Examen 1", "Examen 2", ... ],
    "theoryLessons": [ "Wetgeving", "Voertuigkennis", ... ],
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

#### Course parameter
* `404004` `course_not_found` Course could not be found.
