# Wildfire Tribe Server-side JSON API
JSON API implementation based on https://jsonapi.org/format

## Usage

### To authorise
Step 1: Authorise yourself with Basic HTTP authentication, using API Key (as username) and API Secret (as password) generated from Junction.<br><br>
Step 2: Save the array returned. The array has a column called "access_token". This is the Bearer access token to be used to access data.

### Data handling

#### Single object
GET request on /api/v1/$type/$slug or /api/v1/$type/$id

#### Single attribute of an object
GET request on /api/v1/single/$type/$slug/$attribute or /api/v1/single/$type/$id/$attribute

#### Multiple objects of one $type or search
GET request on /api/v1/$type or /api/v1/search

##### Pagination

##### Sorting

##### Filtering

#### To insert record
POST request on /api/v1/$type/$slug or /api/v1/$type/$id
preferably include: user_id (of creator) and content_privacy

#### To edit or update record
PATCH request on /api/v1/$type/$slug or /api/v1/$type/$id

#### To delete record
DELETE request on /api/v1/$type/$slug or /api/v1/$type/$id
mandatory to include: user_id (of creator)

### Important info
- A type cannot be deleted or modified using API. The only way to modify types is by modifying config/types.json in your Tribe root directory.
- Multple records cannot be deleted or modified using API.