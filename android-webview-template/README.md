## Android Native App (WebView) Template

This repo is a **PHP/HTML website**. To make it an Android “native app”, the simplest approach is a small **Android WebView wrapper** that opens your site:

- Target URL: `https://abagritech.com/`

### What you get in this folder

Copy-paste ready files for an Android Studio project:

- `AndroidManifest.xml`
- `MainActivity.kt`
- `activity_main.xml`

### Build APK (Android Studio)

1. Install Android Studio.
2. Create a new project:
   - **New Project** → **Empty Views Activity**
   - App name: `AB Agritech` (or anything)
   - Package name (example): `com.abagritech.app`
3. After the project is created, replace these files in your Android Studio project with the ones from this folder:
   - `app/src/main/AndroidManifest.xml`
   - `app/src/main/java/<your_package>/MainActivity.kt`
   - `app/src/main/res/layout/activity_main.xml`
4. In Android Studio: **Build → Build Bundle(s) / APK(s) → Build APK(s)**.
5. When it finishes, click **locate** to find the generated APK.

### Notes

- This wrapper loads the live site at `https://abagritech.com/`. The phone must have internet.
- If you want the app to open a **local/offline copy** (assets) instead of the live domain, tell me and I’ll provide that variant.

