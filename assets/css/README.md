# CSS Standardization Documentation

## Overview
This document explains the standardized CSS structure implemented in the project to reduce code duplication and ensure consistent styling across all views.

## Standardized CSS Files

### 1. common.css
Contains base styles and variables used throughout the application:
- CSS variables for colors and button styles
- Base styles for HTML elements (body, html, etc.)
- Common layout elements
- Common heading styles
- Common button styles (delete-btn, update-btn, create-btn, finish-btn, back-btn, password-btn)
- Responsive adjustments

### 2. lists.css
Contains styles specific to list views:
- Table container styles
- Table styles (thead, tbody, tr, td)
- Status indicators for appointments
- Filter container styles
- Table header styles
- Actions column styles
- Photo column styles
- Mobile add button styles
- Responsive styles for tables

### 3. forms.css
Contains styles specific to form views:
- Form container styles
- Form header styles
- Multi-step form styles
- Form group styles
- Form step navigation button styles
- File upload styles
- Image preview styles
- Schedule styles (for doctor schedules)
- Responsive styles for forms

## How to Use

### For List Views
Include the following CSS files in your template:
```html
<link rel="stylesheet" href="/assets/css/common.css">
<link rel="stylesheet" href="/assets/css/lists.css">
<link rel="stylesheet" href="/views/components/sidebar.styles.css">
<link rel="stylesheet" href="/assets/css/usability-improvements.css">
```

### For Form Views
Include the following CSS files in your template:
```html
<link rel="stylesheet" href="/assets/css/common.css">
<link rel="stylesheet" href="/assets/css/forms.css">
<link rel="stylesheet" href="/views/components/sidebar.styles.css">
```

## HTML Structure Guidelines

### List Views
Follow this structure for list views:
```html
<div class="main-content">
    <div class="table-header">
        <h1>Title</h1>
        <a href="...">
            <button class="create-btn">Create Button</button>
        </a>
    </div>

    <div class="filter-container">
        <!-- Filters -->
    </div>

    <div class="table-container">
        <!-- Table -->
        <table>
            <thead>
                <!-- Table headers -->
            </thead>
            <tbody>
                <!-- Table rows -->
                <tr>
                    <td data-label="Column Name">Data</td>
                    <!-- More cells -->
                    <td class="actions-td">
                        <!-- Action buttons -->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### Form Views
Follow this structure for form views:
```html
<div class="center-container">
    <div class="form-container">
        <div class="form-header">
            <a href="..." class="form-back-btn">
                <button class="back-btn">Back</button>
                <span class="back-btn-icon">&#8617;</span>
            </a>
            <h2 class="form-title">Form Title</h2>
        </div>

        <!-- For multi-step forms -->
        <div class="steps">
            <div class="step step-active" data-step="1">Step 1</div>
            <div class="step" data-step="2">Step 2</div>
            <!-- More steps -->
        </div>

        <form>
            <div class="form-step" id="step-1">
                <div class="form-group">
                    <label for="field">Label</label>
                    <input type="text" id="field" name="field">
                </div>
                <!-- More form groups -->
                <button type="button" class="next-btn">Next</button>
            </div>
            <!-- More steps -->
        </form>
    </div>
</div>
```

## Best Practices
1. Always use the standardized CSS files instead of creating new ones
2. Follow the HTML structure guidelines for consistency
3. Use the CSS classes as defined in the standardized files
4. If you need custom styles, create a separate CSS file and import it after the standardized files
5. Use CSS variables from common.css for colors and other shared properties
