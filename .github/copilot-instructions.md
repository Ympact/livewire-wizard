## Repository guidance for AI coding agents

Be concise, modify only what is needed, and prefer changes that preserve public APIs. This project is a Laravel package (Livewire wizard component). The goal is to keep code compatible with Livewire 3 and 4 and Laravel 11+.

- **Style guidelines**:
  - Follow PSR-12 coding standards
  - Use meaningful var and method names (do not abbreviate - not even within local scope/loops)
  - Keep methods short and focused on a single task
  - Make use of laravel and livewire features and best practices
  - Avoid code duplication
  - Write unit tests for all new features and bug fixes
  - Descriptive docblocks for all classes and methods
  - Extract common functionality into reusable methods or classes, use traits and contracts where appropriate.
  - No excessive use of try-catch. Never use try-catch for silencing issues, but throw exception instead.
  - Prefer early returns. Prevent the use of deeply nested condition statements (if more than 3 levels deep, try to refactor into smaller methods or use guard clauses).