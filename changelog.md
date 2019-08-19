### CHANGE LOG:

**2.1.3 August 19, 2019:**
- Bug fix for set an unique name of uploading file. Used **microtime()** in ```LocalUpload``` and ```S3Upload``` models.

**2.1.2 June 12, 2019:**
- Optimize getting module object from a mediafile in **getModule()** function.

**2.1.1 June 01, 2019:**
- Set ability for collable type of preview options location parameter.
  Used in **getPreviewOptions()** module method if **is_callable($previewOptions[$location])**.

**2.1.0 May 18, 2019:**
- Add **urlPrefix parameter** in to ```AlbumController``` for redirect and view links.
- Add **urlPrefixNeighbor** parameter in to ```AlbumController``` for view links of neighbor entity.

**2.0.1 August 9, 2018:**
- Delete duplicates and optimize code.

**2.0.0 August 9, 2018:**
- Code fixes according with the PSR standards:
    - Correct in functions **){** to **) {**.
    - Add space before and after the **@param** function comment option.
    - Renaming controllers and models to a single entity name.
    - Renaming module constants.
    - Some simple code fixes.
- Add **file_exists()** check function in to **sendFile()** of the ```LocalUpload``` model.
- Fixes for README.

**1.0.0 May 15, 2018:**
- Create module with the following options:
    - Upload files to local storage.
    - Upload files to remote Amazon S3 storage.
    - Support file formats: **image**, **audio**, **video**, **application**, **text**.
    - Link uploaded files with external application owners (pages, articles, posts e.t.c...).
    - Manage internal albums: **imageAlbum**, **audioAlbum**, **videoAlbum**, **applicationAlbum**, **textAlbum**, **otherAlbum**.
    - Link uploaded files with internal albums (owners).
    - Link internal albums with the external owners (pages, articles, posts e.t.c...).
- Created documentation.