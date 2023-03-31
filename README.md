# PHP Router component

## !!! This component is under development, not poduction ready yet !!! 

## Features

- Routing
- Middlewares
- Automatic dependency injection in actions and constructor of controllers
- Automatic depencency injection in route closures
- Automatic depencency injection in middlewares
- Route request validator (custom, closure)
- Response, Json response

# Validation rules

Example rules:
```
required
min:5
max:5
min-length:5
max-length:5
regex:/^[0-9]+$/
contains:aa,ab,ac
range:5,10
email
date
integer
int (alias of integer)
float
number (alias of float)
```

Example in controller validation:

```
public function validator(Request $request, JsonResponse $response): ResponseInterface
{
    $validated = $request->validate([
        'par1' => 'required|min:10|max:20',
        'par2' => 'required|min-length:2|max-length:4',
        'par3' => 'regex:/^[0-9]+$/',
        'par4' => function($value) {
            // Return string if value should report an error
            return "'{$value}' validation callback";
        },
        'par5' => function($value) {
            // return null if validation successfull
            return null;
        }
    ]);

    $response->arrayToJson(
        [
            'validated' => $validated,
            'errors' => $request->validationErrors(),
        ]
    );

    return $response;
}
```

... coming soon
