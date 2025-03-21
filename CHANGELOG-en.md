# Changelog

This plugin is a fork of [LLMs-Full.txt and LLMs.txt Generator](https://wordpress.org/plugins/llms-full-txt-generator/).

## [1.9.1] - 2025-03-20 (First release of this fork)

### New Features
- Added automatic update of llms.txt and llms-full.txt files when posts are added, updated, or deleted
- Added scheduled file generation using WordPress Cron (options: hourly, twice daily, daily, weekly)
- Added custom header text feature to add arbitrary text at the beginning of files
- Added debug mode and logging for URL filtering process
- Added direct access support for llms.txt files on frontend

### UI/UX Improvements
- Improved navigation with tabbed interface (Settings, Generate, Help)
- Categorized settings (Basic Settings, Update Settings, Filter Settings)
- Added detailed file information display (last update time, file size)
- Enhanced help section with comprehensive guidance
- Added interactive elements with JavaScript

### Bug Fixes
- Fixed issue where post type headings were not displayed correctly
- Fixed issue where URL exclusion did not work properly
- Fixed to display messages for empty post types

### Enhancements
- Enhanced wildcard pattern matching
- Implemented two-stage check for both exact match and pattern matching
- Added validation for both absolute and relative URLs
- Added URL normalization process (removing trailing slashes)

### Code Quality Improvements
- Optimized memory usage by implementing singleton pattern
- Controlled consecutive updates using transients
- Implemented proper escaping
- Enhanced debugging capabilities
- Added PHP DocBlocks comments

### v1.9.1.1 - 2025-03-21
- Added removal of trailing slashes when registering exclusion and inclusion rules
- Updated help content with information about trailing slash handling

## Original Plugin Version History

### [1.9] - Original Version
- Generation of llms.txt (URL list) and llms-full.txt (full content)
- Post type selection
- Include excerpt functionality
- URL inclusion and exclusion functionality

### [1.8]
- Fixed critical error when no public post types are available
- Added validation to ensure at least one post type is selected

### [1.7]
- Added URL inclusion and exclusion functionality with wildcard support
- Improved error handling for file generation

### [1.0] - Initial Release
- Basic llms.txt and llms-full.txt file generation functionality
