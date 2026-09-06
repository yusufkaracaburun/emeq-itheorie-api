# Detailed information about a students progression

## Request
```http
GET /{reseller}/students/{accessCode}/detailed
```

### Parameters
* `reseller` - `string` - ULID, linking code or chamber of commerce number of the <dfn>reseller</dfn>
* `accessCode` - `string` - Subscription ULID or access code

## Response
### Schema

#### SubscriptionData
| name                         | type                      | description                                                                    |
|------------------------------|---------------------------|--------------------------------------------------------------------------------|
| `id`                         | `Ulid`                    | subscription id                                                                |
| `accessCode`                 | `string`                  | access code for this subscription                                              |
| `createdAt`                  | `DateTime`\|`null`        | timestamp when the subscription was created                                    |
| `expiresAt`                  | `DateTime`\|`null`        | timestamp when the subscription stops working                                  |
| `activatedAt`                | `DateTime`\|`null`        | timestamp when the course was selected for this                                |
| `lastSeenAt`                 | `DateTime`\|`null`        | timestamp when the user was last seen visiting a page                          |
| `course`                     | `CourseData`\|`null`      | the course information for the activated course                                |
| `courseCompletedAt`          | `DateTime`\|`null`        | timestamp when the theory section of the course was first completed            |
| `refresherCourseActivatedAt` | `DateTime`\|`null`        | timestamp when the refresher course was last activated                         |
| `blockedAt`                  | `DateTime`\|`null`        | timestamp of when the user was blocked                                         |
| `blockedReason`              | `string`\|`null`          | reason why this access code is blocked                                         |
| `progress`                   | `string`                  | progress percentage (0.0 - 1.0)                                                |
| `progressUrl`                | `string`\|`null`          | URL to progress overview page, not available if course is null (not activated) |
| `progression`                | `ProgressionData`\|`null` | progression for the student, not available if course is null (not activated)   |

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

#### ProgressionData
| name            | type                                  | description                                                                                 |
|-----------------|---------------------------------------|---------------------------------------------------------------------------------------------|
| `chapters`      | `ProgressionChapterData`\|`null`      | Theory section progression info, can be null if there are no theorie sections in the course |
| `exams`         | `ProgressionExamData`\|`null`         | Practise exam progression info, can be null if there are no practise exam in the course     |
| `theoryLessons` | `ProgressionTheoryLessonData`\|`null` | Theory lessons progression info, can be null if there are no theorie lessons in the course  |

#### ProgressionChapterData
| name        | type     | description                                                                                                                                                                                       |
|-------------|----------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `total`     | `int`    | Total number of chapters in the theory part of the course                                                                                                                                         |
| `completed` | `int`    |                                                                                                                                                                                                   |
| `progress`  | `string` | Progress percentage (0.0 - 1.0) - :information_source: _the percentage is not just percentage of total and completed, it is based of the "expected time to complete a chapter". Some chapters are just short_ |
| `exams` | `array` | :construction: **WIP** _undocumented, just try it!_ |

#### ProgressionExamData
| name        | type     | description                                                                                                                                                                                                                                                        |
|-------------|----------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `total`     | `int`    | Total number of practice exams in the course                                                                                                                                                                                                                       |
| `completed` | `int`    |                                                                                                                                                                                                                                                                    |
| `progress`  | `string` | Progress percentage (0.0 - 1.0) - :information_source: _the percentage is not just percentage of total and completed, it is based of the "expected time to complete an exam". For now all exames have the same expected duration so the end result is the same for now though_ |
| `exams` | `array` | :construction: **WIP** _undocumented, just try it!_ |

#### ProgressionTheoryLessonData
| name       | type     | description                                                                                                                                            |
|------------|----------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| `total`    | `int`    | The total amount of theory lessons in the course                                                                                                       |
| `attended` | `int`    | The amount of theory lessons followed                                                                                                                  |
| `progress` | `string` | Progress percentage (0.0 - 1.0) :information_source: _unlike the other two this is just a division of total and attended, but for consistencies sake its here too_ |
| `exams` | `array` | :construction: **WIP** _undocumented, just try it!_ |

### Example
```json
{
    "id": "01GYFBXGA6C3BZD16WNK6FPQ4P",
    "accessCode": "128b82eecaf9decc",
    "expiresAt": "2033-03-01T00:00:00+01:00",
    "activatedAt": "2023-04-20T14:58:34+02:00",
    "lastSeenAt": "2023-08-29T18:19:03+02:00",
    "course": {
        "id": "01GYFBWMYGGARXBN40X7FFDCNZ",
        "license": "B",
        "locale": "NL",
        "title": "Theoriecursus personenauto",
        "description": "Online theorie leren voor het CBR theorie-examen auto, motor, scooter, snorfiets, bromfiets, speed-pedelec of brommobiel.",
        "image": "https://test.itheorie.nl/assets/images/course/fallback.jpg",
        "publishedAt": "2023-04-20T14:58:06+02:00",
        "updatedAt": "2023-08-29T18:20:56+02:00"
    },
    "courseCompletedAt": "2023-04-20T14:58:35+02:00",
    "refresherCourseActivatedAt": null,
    "blockedAt": null,
    "blockedReason": null,
    "progress": "0.8134",
    "progressUrl": "https://test.itheorie.nl/voortgang/wLz4fPuVbmN7-RQ8YsO0kwAaP-ysJ4OTggl87R-CCk_DS8~",
    "progression": {
        "chapters": {
            "total": 9,
            "completed": 9,
            "progress": "1",
            "chapters": [
                {
                    "title": "Wetgeving",
                    "results": [
                        {
                            "completedAt": "2023-04-20T14:58:34+02:00"
                        }
                    ]
                },
                {
                    "title": "Voertuigkennis",
                    "results": [
                        {
                            "completedAt": "2023-04-20T14:58:34+02:00"
                        }
                    ]
                }
            ]
        },
        "exams": {
            "total": 50,
            "completed": 10,
            "progress": "0.2",
            "exams": [
                {
                    "title": "Examen 1",
                    "results": [
                        {
                            "completedAt": "2023-04-20T14:58:35+02:00",
                            "mappedScore": 65,
                            "achievedScore": 60,
                            "passed": true
                        }
                    ]
                },
                {
                    "title": "Examen 2",
                    "results": [
                        {
                            "completedAt": "2023-04-20T14:58:35+02:00",
                            "mappedScore": 65,
                            "achievedScore": 54,
                            "passed": false
                        }
                    ]
                },
                {
                    "title": "Examen 3",
                    "results": []
                }
            ]
        },
        "theoryLessons": {
            "total": 9,
            "attended": 9,
            "reservations": [
                {
                    "title": "Wetgeving",
                    "reservations": [
                        {
                            "createdAt": "2023-04-20T14:58:56+02:00",
                            "theoryLessonStartsAt": "2023-04-30T10:00:00+02:00",
                            "theoryLessonEndsAt": "2023-04-30T10:50:00+02:00",
                            "hasAttended": true
                        }
                    ]
                },
                {
                    "title": "Voertuigkennis",
                    "reservations": [
                        {
                            "createdAt": "2022-08-05T19:30:00+02:00",
                            "theoryLessonStartsAt": "2022-08-07T19:30:00+02:00",
                            "theoryLessonEndsAt": "2022-08-07T20:30:00+02:00",
                            "hasAttended": true
                        }
                    ]
                }
            ],
            "progress": "1"
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

#### Subscription parameter
* `400011` `invalid_subscription_parameter` Subscription parameter is expected to a ULID or chamber of commerce number, if the value matched neither of the expected formats this message is shown.
* `400012` `invalid_access_code_parameter` The subscription parameter matches an acces code, but the access code is not valid (could be a typo, or simply wrong).
* `404005` `subscription_not_found_by_access_code` Student could not be found at iTheorie, do you have the correct access code?
* `404006` `subscription_not_found_by_id` Student could not be found at iTheorie.

#### Miscellaneous
* `403006` `reseller_can_not_view_subscription` The reseller is not allowed to see the progression of the student, it must first be permitted by the student in the study section of iTheorie or using `permissionToShareProgress` in the [:link: `POST /{reseller}/purchases`](reseller-purchases-post.md) request.
