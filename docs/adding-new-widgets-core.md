# Adding new Widget

This guide will help you add new functionality to the core application. 
If you want to add your widget to the front-end, refer to [this](adding-new-widgets-web.md) guide. 

## Before You Begin
Make sure you read the previous documentation before starting, also get a good understanding
of how PBS specific entities relate to each other. You can generate a ERM inside the `pg-admin` service container.  

## Content
- [Entities](#entities)
- [Aggregating Data](#aggregating-data)
    - [How to implement `aggregate`](#how-to-implement-the-aggregate-function)
- [Data Providers](#data-providers)
- [Data Transfer Object (DTO)](#data-transfer-object-dto)
- [API Controller](#api-controller)
    - [Adding The Route Configuration](#adding-the-route-configuration)

## Entities 

Each Widget has at least one table in the database. This table(s) is used to store the aggregated data.
Make sure you know what kind of data is needed to visualize your widget or chart in the front-end before you
start coding.

- Add a new Entity to `src/Entity/` with a prefix "Widget". 
- Extend the `Widget` Entity.
- Prefix the table name with `hc_widget_` you can check how this is done in existing entities.
- Add the properties you need to store.

## Aggregating Data

Each widget has its own aggregation logic. We use a registry pattern to ease the dat import procedure. 
Check the data import docs if you haven't this is a crucial part of the application which needs to be understood.

- Add a new `Aggregator` inside `src/Service/Aggregator/`.
- Make sure to add the `Aggregator` suffix to the class name.
- Extend the `WidgetAggregator` class.
- Make sure the parent constructor is called properly with all needed arguments.

This will force you to implement:
 - `getName(DateTime $startDate = null)`
 - `aggregate(DateTime $startDate = null)`

Make sure you add a `private const NAME = "widget.<WIDGET_NAME>";` property to your aggregator so
that aggregate will be called when importing data. Additionally, you need to add the following to `config/services.yml` 
```yaml
    App\Service\Aggregator\\<YOUR_AGGREGATOR_CLASS>:
        tags:
            - { name: 'widget.aggregator', key: 'widget.<WIDGET_NAME>' }
```

This will ensure that your aggregator is injected into the `AggregatorRegistry` automatically by the Symfony framework.

### How to implement the `aggregate` Function

> Note: This might be dependent on your widgets requirements.

The `aggregate` function has a `$startDate` parameter that you will need to handle. This `$startDate` defines
the beginning date of the import. We aggregate data for each first day of each month and additionally 
for the current date. 

The baseline logic you will probably need to add is the following:

```php
/**
 * @param DateTime|null $startDate
 * @throws Exception
 */
public function aggregate(DateTime $startDate = null)
{
    $minDate = $startDate !== null ? $startDate : new DateTime(self::AGGREGATION_START_DATE);
    $maxDate = new DateTime();
    $startPointDate = clone $minDate;

    while ($startPointDate->getTimestamp() < $maxDate->getTimestamp()) {
        $startPointDate->add(new DateInterval("P1M"));
        $startPointDate->modify('first day of this month');

        if ($startPointDate->getTimestamp() > $maxDate->getTimestamp()) {
            $startPointDate = clone $maxDate;
        }
        
        // the logic for each date we aggregate data for goes here
    }
    
    // ...   
}
```

The code above will make sure the "while loop" iterates over the required dates. 
The `$startPointDate` will be set to the current date data needs to be aggregated for.

It is very likely that you will need to aggregate data by each group (Abteilungen/Stufen). In this case you should
check existing Aggregators for some example code on how this is currently done. I recommend checking the
`DemographicGroupAggregator` class since it is a perfect example on how data is handled for each main-group ("Abteilung")
and its sub-groups ("Stufen").
  
If your aggregation will run a lot of queries make sure to call `flush()` and `clear()` on the Doctrine `EntityManager`
to avoid memory leaks.

## Data Providers

Data Providers (in `src/Service/DataProvider/`)  are used by the API controller to get the data for a specific widget from the widget table and do some
additional transformation if needed. You will need to create a one or two Data Provider classes for your widget.
The reason for this is that some widgets will show data for a date range and or a single date. 

- Create one or two data provider inside `src/Service/DataProvider` as required by your widget.
- Add an appropriate suffix to each class ("DateRangeDataProvider" or "DateDataProvider")
- Extend the `WidgetDataProvider`, it will provide some helper methods and constants you will probably need

Depending on what kind of provider you created "DateRange" or "Date" you will have to add the following params to your
function. 

For "Date":
`public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes)`

For "DateRange":
`public function getData(Group $group, string $from, string $to, array $subGroupTypes, array $peopleTypes)`

Parameters explained:

- `Group $group` The currently selected main group ("Abteilung")
- `string $from` The selected "from" date (only for DateRange provider)
- `string $to` The selected "to" date (only for DateRange provider)
- `string $date` The selected date (only for Date provider)
- `array $subGroupTypes` The selected sub-group ("Stufen") types ("Biber", "Woelfe", ...)
- `array $peopleTypes` The selected type of people (this relates to the roles) ("Leiter" / "Teilnehmer")

Good examples for additional details are the `MembersGroupDateDateProvider` and `MembersGroupDateRangeDateProvider`.
These use the data that was aggregated by the `DemographicGroupAggregator` mentioned previously.

## Data Transfer Object (DTO)

Since the front-end will fetch the widget or chart data through a REST API we provide DTO to
have a defined data model between domains (core / web). All DTO Model classes are inside `src/DTO/Model`.
Additionally, there are mappers (`src/DTO/Mapper/`) which should be used to transform data to a desired DTO. 

If you want to show the data in a chart you can use one of the existing models:
- `PieChartDataDTO`
- `LineChartDataDTO`
- `BarChartDataDTO` 

Alternatively you can add a new DTO model as required.

## API Controller

The controller will handle the incoming request when the front-end requests data for a specific widget.

- Create a new controller inside `src/Controller/API/Widget/`
- Create a method to handle the request (preferably with the prefix "get")
- Extend the `WidgetController` so the dependency injection will work correctly

Now you can use some dependency injection magic which was already provided for you. 
Add your providers and `DateAndDateRangeRequestData $requestData` as function parameters.
The `DateAndDateRangeRequestData` object will provide information needed to decide which data provider will need
to be called (`$from`, `$to` and `$date`). Check `MembersGroupController` if you want to get an easy example.

After getting the necessary data from your provider class you can simply serialize and return the data using the provided
`$this->json($data)` function. 

>If you want to know how the `$requestData` is injected and resolved you can check 
>`src/EventListeneer/WdigetControllerListener`.

### Adding The Route Configuration

Now all that's left to do is add a new route, so your controller method is called.

To do this, add a new yaml object to the `config/routing_api.yml`: 
```yaml
name_for_your_route:
  path: /groups/{groupId}/path-for-your-route
  methods: GET
  controller: App\Controller\Api\Widget\<YOUR_CONTROLLER_CLASS>:<YOUR_CONTROLLER_METHOD>
```

:+1::tada: Congrats you added your new widget to the core! :tada::+1:

