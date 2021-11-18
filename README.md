# Wildfire Tribe Server-side JSON API
JSON API implementation based on https://jsonapi.org/format

## Usage

### To authorise
1. Authorise yourself with Basic HTTP authentication, using API Key (as username) and API Secret (as password) generated from Junction.
2. Save the array returned. The array has a column called "access_token". This is the Bearer access token to be used to access data.

### Data handling

#### Read Single object
GET request on `/api/$type/$slug` or `/api/$id`

#### Read Multiple objects of one $type or search
GET request on `/api/$type` or `/api/search`

##### Pagination
GET request on `/api/$type?index=0&limit=25`

##### Sorting

##### Filtering
Data can be cherry picked passing the "filter" query in URL with the desired data.  
`GET` request using `?filter=name,age,location,email`

#### To _create_ record
POST request on `/api/$type`
preferably include: user_id (of creator) and content_privacy

#### To _edit/update_ record
PATCH request on `/api/$type/$slug` or `/api/$type/$id`

#### To _delete_ record
DELETE request on `/api/$type/$slug` or `/api/$type/$id`
mandatory to include: user_id (of creator)

#### Upload interface
**POST** `/api/file-upload`
1. Include the js file `tribe_upload.js` under `dist` in your project (or use it as a reference to implement your own)
2. create a button that you want to program for upload with a `data-target='#input-file'` ('#input-file' can be anything you want, but mention a target)
3. Initialize the code by passing selector of your event initiator (i.e. button created in step 2) and defining the api URL `tribeUploadUrl`
```html
<form action="#">
    <input type="file" name="upload[]" id="upload" class="form-control mb-3" multiple>
    <button type="submit" class="btn btn-primary" id="form-submit" data-target="#upload">Submit</button>
</form>
```

```javascript
tribeUploadButton('#form-submit');
let tribeUploadUrl = '/api/file-upload'
```

### Important info
- A type cannot be deleted or modified using API. The only way to modify types is by modifying config/types.json in your Tribe root directory.
- Multple records cannot be deleted or modified using API.
