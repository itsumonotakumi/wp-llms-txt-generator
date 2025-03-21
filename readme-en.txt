=== LLMS TXT and Full TXT Generator ===
Contributors: itsumonotakumi, rankth
Tags: llm, ai, txt, content export, large language model
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.9.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Exports your WordPress site content to llms.txt and llms-full.txt files for use as training data for LLMs (Large Language Models).

== Description ==

This plugin automatically exports your site's posts and pages to llms.txt and llms-full.txt files. The generated files can be used as training data for LLMs (Large Language Models).

"LLMs-Full.txt and LLMs.txt Generator" is a fork of the [LLMs-Full.txt and LLMs.txt Generator](https://wordpress.org/plugins/llms-full-txt-generator/) published on WordPress.org, with extended functionality. Thanks to the original developer [rankth](https://profiles.wordpress.org/rankth/) for their contribution.

= Multilingual Support =

* Japanese (Default)
* English

= Disclaimer =

**We do not compensate for any inconvenience or accident caused by this program. Please use it at your own risk.**

= Key Features =

* Converts content from specified post types into structured text files
* Generates two types of files: llms.txt (URL list) and llms-full.txt (full content)
* **New Feature**: Automatically updates files when posts are added, updated, or deleted
* **New Feature**: Scheduled automatic generation using WordPress Cron (hourly/twice daily/daily/weekly)
* **Improvement**: Enhanced URL pattern filtering (including trailing slash handling)
* **New Feature**: Custom header text addition
* **New Feature**: Debug mode with detailed URL processing logs
* **Improvement**: User-friendly tab-based admin interface

= How to Use URL Filters =

In URL filters, you can use wildcards (*) to specify patterns:

* `/blog/*` - targets all pages in the blog directory
* `*/2023/*` - targets all URLs containing 2023

**Note**: Trailing slashes in URLs are automatically removed, so `/contact/` and `/contact` are treated the same.

= Contact Information =

* Email: llms-txt@takulog.info
* Homepage: https://mobile-cheap.jp
* X (Twitter): https://x.com/itsumonotakumi
* Threads: https://www.threads.net/@itsumonotakumi
* YouTube: https://www.youtube.com/@itsumonotakumi

== Installation ==

1. Upload the plugin or search for it from the WordPress admin panel and install
2. Activate the plugin
3. Go to "Settings" → "LLMS.txt Generator Settings" to configure
4. Select which post types to include in the files and adjust other settings as needed
5. Click the "Generate LLMS.txt Files" button in the "Generate" tab to create the files

== Screenshots ==

1. Settings screen - Configure various post types, custom headers, URL filters, debug mode and more
2. Generation screen - Check file generation status and details about the generated files

== Frequently Asked Questions ==

= What's the difference between llms.txt and llms-full.txt? =

llms.txt contains only a list of URLs and post titles, while llms-full.txt includes the full content of the posts.

= What should I do if URLs are not being excluded correctly? =

Enable Debug Mode and check the URL processing logs. Logs are saved to `wp-content/plugins/llms-txt-full-txt-generator/logs/url_debug.log`.

= What is the difference between auto-update and scheduled execution? =

Auto-update updates the files when posts are added, updated, or deleted, while scheduled execution automatically generates files at a set frequency (hourly/twice daily/daily/weekly).

== Changelog ==

= 1.9.2 =
* Added multilingual support (Japanese and English)
* Improved readme.txt and added screenshots
* Minor UI/UX adjustments
* Internal process optimization

= 1.9.1 =
* Added automatic update of files when posts are added, updated, or deleted
* Added scheduled file generation using WordPress Cron
* Added custom header text feature to add arbitrary text at the beginning of files
* Added debug mode and logging for URL filtering process
* Improved navigation with tabbed interface
* Fixed issue where URL exclusion did not work properly
* Added URL normalization process for URLs with trailing slashes
* Fixed issue where post type headings were not displayed correctly

= 1.9.1.1 =
* Added removal of trailing slashes when registering exclusion and inclusion rules
* Updated help content with information about trailing slash handling

= 1.9 =
* Original version by rankth

== Upgrade Notice ==

= 1.9.2 =
This update adds multilingual support (Japanese and English) and includes UI improvements.

= 1.9.1 =
This update includes many feature enhancements such as automatic updates and improved URL exclusion processing. If you've had issues with URL filtering, they may be resolved with this update.

== Feedback & Support ==

This plugin is developed as open source on [GitHub](https://github.com/itsumonotakumi/llms-txt-full-txt-generator). Please contact via email for bug reports or feature requests.
