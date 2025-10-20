### CHANGE LOG:

**3.2.8 October 20, 2025:**
- Improve scrutinizer config to use Composer 2.
- Update Readme.

**3.2.7 January 23, 2023:**
- Readme fix.

**3.2.6 January 22, 2023:**
- Improve scrutinizer config.

**3.2.5 January 18, 2023:**
- Upgrade copyright year.

**3.2.4 April 18, 2021:**
- Bug fix: **Unparenthesized `a ? b : c ? d : e` is not supported**.

**3.2.3 April 17, 2021:**
- Bug fix: **Call to a member function validateCsrfToken() on string**.

**3.2.2 February 23, 2021:**
- Upgrade copyright year.

**3.2.1 September 12, 2020:**
- Allow set `null` for one width or height in ThumbConfig.

**3.2.0 September 12, 2020:**
- Optimize `deleteMediafiles()` method in `MediaFilesTrait`. Add protection to physical delete multiplied files, which are related more then one owner.
- Optimize owner's entity classes.

**3.1.1 August 10, 2020:**
- Add module attribute `useInitialThumbsConfig` with default value **true**.

**3.1.0 August 07, 2020:**
- Increase `alt` and `title` string sizes to 128 for `mediafiles` table.

**3.0.2 July 22, 2020:**
- Use `array_merge()` instead `ArrayHelper::merge()` to merge default **thumb** and **preview** configs with custom.

**3.0.1 July 17, 2020:**
- Bug fix for `registerTranslations()` method. Set it static.

**3.0.0 July 15, 2020:**
- Set `string` sizes for migration columns no more 128 (and 64) to support old MySql database versions.
- Solve the installation issue: `Syntax error or access violation: 1071 Specified key was too long; max key length is 1000 bytes`.
- Fixes for small bugs.

**2.2.2 June 23, 2020:**
- Modify README syntax.

**2.2.1 June 20, 2020:**
- Bug fix for **getThumbUrl()** method in `Mediafile` model.

**2.2.0 June 15, 2020:**
- Change saving local file **url** to DB.
- Add **publicBaseUrl** param to Module for full local files url.
- Add **getViewUrl()** to getting file's url with a public base url just for local storage.

**2.1.5 June 9, 2020:**
- Documentation upgrade. Change link to personal site.

**2.1.4 August 19, 2019:**
- Upgrade of the copyright time and add a personal site link.

**2.1.3 August 19, 2019:**
- Bug fix for set an unique name of uploading file. Used **microtime()** in `LocalUpload` and `S3Upload` models.

**2.1.2 June 12, 2019:**
- Optimize getting module object from a mediafile in **getModule()** function.

**2.1.1 June 01, 2019:**
- Set ability for collable type of preview options location parameter.
  Used in **getPreviewOptions()** module method if **is_callable($previewOptions[$location])**.

**2.1.0 May 18, 2019:**
- Add **urlPrefix parameter** in to `AlbumController` for redirect and view links.
- Add **urlPrefixNeighbor** parameter in to `AlbumController` for view links of neighbor entity.

**2.0.1 August 9, 2018:**
- Delete duplicates and optimize code.

**2.0.0 August 9, 2018:**
- Code fixes according with the PSR standards:
    - Correct in functions **){** to **) {**.
    - Add space before and after the **@param** function comment option.
    - Renaming controllers and models to a single entity name.
    - Renaming module constants.
    - Some simple code fixes.
- Add **file_exists()** check function in to **sendFile()** of the `LocalUpload` model.
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