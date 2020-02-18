# WP-NDJSON
A simple plugin to export/create a NDJSON file from your WordPress content.

## Usage

### Installation

After install, WP-NDJSON will create a `/wp-ndjson` in the uploads directory.

### Creating an index

No index is created after installation. This is to avoid unncessary index builds while installing on sites with large datasets.

To create the NDJSON index file, navigate to `Settings / WP-NDJSON` in the WordPress dashboard.

### Updating an index

On post save or post edit, WP-NDJSON will query the existing index file and update the post content.

## What is NDJSON

NDJSON is Newline delimited JSON. It's a convenient format for storing or streaming large amounts of JSON data. For more info on NDJSON refer to [ndjson.org](http://ndjson.org/).

## Why was this plugin created?

This plugin was a byproduct of another build. While creating a plugin to export WordPress posts as JSON data, I unintentionally wrote a function that exported the data as NDJSON.

I find the NDJSON format interesting and belive it has a lot of potetional but I don't have a specific use case for it yet. For now, I'll release this as it is in case anyone finds it helpful.