---
title: Fix plugin discovery order
issue: NEXT-21996
author: Maximilian Rüsch
author_email: maximilian.ruesch@pickware.de
author_github: maximilianruesch
---
# Core
* Changed the `PluginFinder` to consider plugins found in the `vendor` folder over plugins found in the `custom/plugins` folder.
