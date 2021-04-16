# Adding new Widget

This guide will help you add new widgets to the front-end application.  

## Before You Begin
Make sure you read the previous documentation before starting.  

## Contents

- [Adding A New Widget Component](#adding-a-new-widget-component)
    - [Additional Setup](#additional-setup)
- [Configure The Widget](#configure-the-widget)
- [Widget Grid](#widget-grid)
- [Adding charts](#adding-charts)

## Adding A New Widget Component

This component should handle displaying all widget related data. 

- Add a new component in `src/app/widget/components/widgets`
- Extend the `WidgetComponent` class

### Additional Setup

- Add a `public static WIDGET_CLASS_NAME =  'NameOfComponentClass'` property.
- Inject the constructor with the `WidgetTypeService`
- Call the `super()` parent constructor with `WidgetTypeService` and the class 
of the widget component as second parameter.

For an example you can check `src/app/widget/components/widgets/members-group/members-group.component.ts`.

Why do we need this? These widgets are dynamically created by 
`src/app/widget/components/widget-wrapper/widget-wrapper.component.ts` at runtime (`initWidgets()`). 
To achieve this behaviour some info needs to be passed to the angular component factory that's why 
`WidgetComponent` needs to be extended, and we need the type of the component class. 
 
## Configure The Widget

To know where the data needs to be fetched a `Widget` object needs to be added to
`WidgetState` (`src/app/store/state/widget.state.ts`).
The `Widget` (`src/app/shared/models/widget.ts`) class constructor takes the following arguments:

```typescript
export class Widget {
  constructor(
    public uid: string,
    public className: string,
    public rows: number,
    public cols: number,
    public supportsRange: boolean,
    public supportsDate: boolean,
    public data: any = null
  ) {
  }
}
```
- `rows` amount of rows that your widget takes up in the grid
- `cols` amount of columns that your widget takes up in the grid
- `supportsRange` True if your widget provides date range data
- `supportsDate` True if your widget provides single date selection data

You will need to pass all except `data` to when adding a new object to the `widgetData`.

```typescript
private widgetData = new BehaviorSubject<Widget[]>([
    ...,
    new Widget('path-of-endpoint', 'WidgetComponentClassName', 1, 2, true, true)
]);
```

Finally, you need to register your widget component inside the widget module: `src/app/widget/widget.module.ts`.

```yaml
providers: [
    {
      provide: 'widgets',
      useValue: [
        LeaderOverviewComponent,
        ...,
        YourWidgetComponent
      ]
    }
  ]
```

## Widget Grid

All widgets are displayed in a grid, the directive `src/app/widget/components/widget-wrapper/widget-grid.directive.ts`
will handle displaying the widget. The grid has a maximum of 2 columns. 

You need to modify it according to your widgets requirements. 
If your widgets supports date range adjust the `rangeArea`, if it supports a single date selection adjust `dateArea`. 
The css property `grid-template-areas` of the `widget-grid-container` will be set to `rangeArea` and `dateArea`
according to the filter selection.

## Adding charts

We use the `@swimlane/ngx-charts` package to add charts to widgets. Refer to the documentation of the package
for implementing charts in the templates of your components.