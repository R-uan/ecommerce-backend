# Documentation in progress

### Custom query search `api/products/search?param[operation]=value`

`param` is the database column that you want to conduct the search <br />
`operation` is the comparasion operator equivalent in alphanumeric characters <br />
`value` value you're looking for in the column <br />

### Operations

-   eq : Equals (=)
-   lk : Like (ilike)
-   lt : Less than (<)
-   gt : Greater than (>)
-   lte : Less or equal to (<=)
-   gte : Grater or equal to (>=)

### Product Params and supported operations

-   'name' => ['lk'],
-   'category' => ['eq'],
-   'availability' => ['eq'],
-   'price' => ['eq', 'lt', 'gt', 'lte', 'gte'],

### Example /api/products/search?name[lk]=milenium

The URL will look into the database and retrieve all products that has 'milenium' in their name. <br />
The SQL called in the database would be something like: `SELECT * FROM products WHERE name ILIKE '%milenium%';` + the join operation to get the manufacturer. <br />

> On this day, the ILIKE operation is exclusive to **Postgres** and might not work on other databases.
