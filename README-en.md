# LLMS TXT and Full TXT Generator

![WordPress Version](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP Version](https://img.shields.io/badge/PHP-7.0%2B-purple)
![License](https://img.shields.io/badge/License-GPL%20v2-green)
![Version](https://img.shields.io/badge/Version-2.0-orange)

A WordPress plugin that automatically exports posts and pages to llms.txt and llms-full.txt files, which can be used as training data for LLMs (Large Language Models).

> This plugin is a fork of [LLMs-Full.txt and LLMs.txt Generator](https://wordpress.org/plugins/llms-full-txt-generator/) with extended functionality. Thanks to the original developer [rankth](https://profiles.wordpress.org/rankth/) for their contribution.

## Overview

This plugin generates llms.txt and llms-full.txt files in the root directory of your WordPress site for LLMs. This enables AI models to efficiently understand and properly reference your website content.

In addition to the original features, we've made numerous enhancements including automatic updates and improved URL filtering.

## Disclaimer

**We do not compensate for any inconvenience or accident caused by this program. Please use it at your own risk.**

## Key Features

- Converts content from specified post types into structured text files
- Generates two types of files: llms.txt (URL list) and llms-full.txt (full content)
- **New Feature**: Automatically updates files when posts are added, updated, or deleted
- **New Feature**: Scheduled automatic generation using WordPress Cron (hourly/twice daily/daily/weekly)
- **Improvement**: Enhanced URL pattern filtering (including trailing slash handling)
- **New Feature**: Custom header text addition
- **New Feature**: Debug mode with detailed URL processing logs
- **Improvement**: User-friendly tab-based admin interface
- **New Feature**: Multilingual support (Japanese and English)

## Installation

1. Download the latest ZIP file from [Releases](https://github.com/itsumonotakumi/llms-txt-full-txt-generator/releases)
2. From WordPress admin panel, go to "Plugins" → "Add New" → "Upload Plugin"
3. Upload the downloaded ZIP file and install
4. Activate the plugin
5. Go to "Settings" → "LLMS.txt Generator Settings" to configure

Alternatively, you can extract the files directly into your `wp-content/plugins/` directory.

## Usage

### Basic Settings

1. Go to "Settings" → "LLMS.txt Generator Settings"
2. In the "Basic Settings" tab, select which post types to include in the files
3. Add custom header text if needed
4. Configure the "Include Excerpts" option (whether to include post excerpts in llms-full.txt)

### Update Settings

1. Enable "Auto-update on Post Changes" to automatically generate files when content is updated
2. Enable "Scheduled Generation" and set the frequency to automatically generate files at specified intervals
3. Enable "Debug Mode" to generate detailed logs of URL processing

### Filter Settings

1. Specify patterns in "Include URLs" to include only URLs that match those patterns (if empty, all URLs are included)
2. Specify patterns in "Exclude URLs" to exclude URLs that match those patterns

### File Generation

Click the "Generate LLMS.txt Files" button in the "Generate" tab to create files based on your settings.

## How to Use URL Filters

In URL filters, you can use wildcards (*) to specify patterns. For example:

- `/blog/*` - targets all pages in the blog directory
- `*/2023/*` - targets all URLs containing 2023

Input examples:
```
https://example.com/page1
https://example.com/page2
/contact
/about-us
*/exclude-this-part/*
```

**Note**: Trailing slashes in URLs are automatically removed, so `/contact/` and `/contact` are treated the same.

## Troubleshooting

If URLs are not being excluded correctly, check the following:

1. Enable Debug Mode and check the URL processing logs
2. Verify that the URL format is correct (absolute and relative URLs)
3. Check if your wildcard usage is appropriate

Debug logs are saved to `wp-content/plugins/llms-txt-full-txt-generator/logs/url_debug.log`.

## Main Changes from the Original

- Added post type heading display
- Significantly improved URL exclusion processing (including trailing slash normalization)
- Added automatic update functionality
- Added scheduled execution
- Added custom header text functionality
- Added debug mode
- Improved UI/UX (tab interface, detailed file information display, etc.)

For detailed change history, see [CHANGELOG.md](CHANGELOG-en.md).

## License

GPL v2 (based on the original plugin "LLMs-Full.txt and LLMs.txt Generator v1.9")

## Developer Information

- Modified by: [Itsumonotakumi](https://twitter.com/itsumonotakumi)
- Blog: [Gadget Review Takumi](https://mobile-cheap.jp)
- Original plugin developer: [rankth](https://profiles.wordpress.org/rankth/)

## Contact Information

- Email: [llms-txt@takulog.info](mailto:llms-txt@takulog.info)
- Homepage: [https://mobile-cheap.jp](https://mobile-cheap.jp)
- X (Twitter): [@itsumonotakumi](https://x.com/itsumonotakumi)
- Threads: [@itsumonotakumi](https://www.threads.net/@itsumonotakumi)
- YouTube: [@itsumonotakumi](https://www.youtube.com/@itsumonotakumi)
