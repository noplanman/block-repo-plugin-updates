=== Block Repo Plugin Update ===

Contributors: noplanman
Donate link: https://noplanman.ch/donate
Tags: block, repo, repository, plugin, update, dev, developer, development
Requires at least: 4.7.0
Tested up to: 5.0.3
Stable tag: 1.0.0
Requires PHP: 5.4
Author URI: https://noplanman.ch
Plugin URI: https://git.feneas.org/noplanman/block-repo-plugin-updates
License: Unlicense
License URI: https://unlicense.org/

Blocks plugin updates for any plugin whose folder looks like a code repo.

== Description ==

🔒 Blocks plugin updates for any plugin whose folder looks like a code repo. (at the moment only git and Subversion)
(Based on <a href="https://wordpress.org/plugins/block-specific-plugin-updates/">Block Specific Plugin Updates</a>)

🛠 Intended purely for developers!

😇 There are no settings, activate and forget...

🔮 A single filter to extend the list of relative file paths that denote a repo plugin.
- `brpu_repo_files`: Filter that returns an array of file paths.

== Installation ==

🙄 If you're looking for instructions here, this plugin isn't for you.

== Frequently Asked Questions ==

= What does this plugin even do? =

If you've ever worked on a plugin fork and a newly released version gets updated and kills your code, you'll know.
Losing code sucks! Got better stuff to do.

= What's the name of the filter again? =

😉 `brpu_repo_files`, and remember, it must return an array!

== Changelog ==

= 1.0 =
* First release.
