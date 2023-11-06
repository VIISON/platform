---
title: Fix silencing flow errors inside transaction
issue:
author: Maximilian Rüsch
author_email: maximilian.ruesch@pickware.de
author_github: maximilianruesch
---
# Core
* Changed the `SetOrderStateAction` such that any exceptions are rethrown if the action is executed inside a nested transaction that does not have save points enabled for transaction nesting.
