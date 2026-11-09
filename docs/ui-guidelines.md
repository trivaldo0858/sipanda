# Posyandu-In UI Quality Guidelines

## Design Direction

Build a practical healthcare information system.

The design should feel:

- Professional
- Calm
- Clear
- Trustworthy
- Human-centered

Avoid making the interface look like a generic
AI-generated SaaS dashboard.

## Before Designing

Always inspect:

- Existing layout
- Existing sidebar
- Existing typography
- Existing button styles
- Existing card patterns
- Existing form components
- Existing responsive behavior

Do not create a new design system for every page.

## Layout

- Use consistent page padding.
- Align section headings.
- Use consistent content widths.
- Avoid unnecessary nested containers.
- Avoid excessive whitespace.
- Do not compress important health information.
- Maintain readable line lengths.

## Typography

Use a clear hierarchy:

- Page title
- Section title
- Card title
- Supporting text
- Metadata

Do not use arbitrary font sizes.
Do not mix unrelated font families.
Do not make every heading oversized.

## Cards

- Use cards only when they group related information.
- Maintain consistent radius and padding.
- Use borders or subtle shadows, not both excessively.
- Avoid excessive nested cards.
- Keep metric values readable.
- Align icons and labels consistently.

## Colors

Use the existing Posyandu-In palette.

Primary web blue: #155DFC

Do not introduce new accent colors without
a clear semantic purpose.

Use colors consistently:

- Blue for primary actions.
- Green for appropriate positive states.
- Amber for attention states.
- Red for destructive or serious states.
- Slate for neutral information.

Never use color alone to communicate status.

## Healthcare Data

- Never fabricate child measurements.
- Never fabricate examination history.
- Never fabricate prediction results.
- Show a clear empty state when data is unavailable.
- Distinguish monitoring results from predictions.
- Keep medical explanations understandable.
- Avoid presenting predictions as diagnoses.

## Interaction

Every button must have a clear action.

Forms need:

- Labels
- Validation feedback
- Loading feedback
- Error feedback

Tables and cards must work on smaller screens.

## Animation

Use animation only when it improves feedback
or clarifies a transition.

Avoid decorative animation.

## Final UI Review

Before finishing, check:

1. Does the page match the existing Posyandu-In design?
2. Is the information hierarchy clear?
3. Are headings consistent?
4. Are spacing and alignment consistent?
5. Is the page responsive?
6. Do all buttons work?
7. Are loading and empty states handled?
8. Is any content fabricated?
9. Did the change break existing functionality?

Fix visual inconsistencies before reporting completion.
