# Wildfire Tribe Server-side JSON API
JSON API implementation based on https://jsonapi.org/format

## Usage

### To authorise
Step 1: Authorise yourself with Basic HTTP authentication, using API Key (as username) and API Secret (as password) generated from Junction.<br><br>
Step 2: Save the array returned. The array has a column called "access_token". This is the Bearer access token to be used to access data.

### Data handling

#### Read Single object
GET request on /api/s/$type/$slug or /api/s/$id

#### Read Single attribute of an object
GET request on /api/s/$type/$slug/$attribute or /api/s/$id/$attribute

#### Read Multiple objects of one $type or search
GET request on /api/s/$type or /api/s/search

##### Pagination
GET request on /api/s/$type?index=0&limit=25

##### Sorting

##### Filtering

#### To _create_ record
POST request on /api/s/$type
preferably include: user_id (of creator) and content_privacy

#### To _edit/update_ record
PATCH request on /api/s/$type/$slug or /api/s/$type/$id

#### To _delete_ record
DELETE request on /api/s/$type/$slug or /api/s/$type/$id
mandatory to include: user_id (of creator)

### Important info
- A type cannot be deleted or modified using API. The only way to modify types is by modifying config/types.json in your Tribe root directory.
- Multple records cannot be deleted or modified using API.
