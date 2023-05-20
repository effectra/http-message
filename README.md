# Effectra HTTP Message Library

A lightweight PHP library for working with HTTP messages.

## Installation

Install the library using Composer:

```bash
composer require effectra/http-message
```

## Usage

### Creating a Request

```php
use Effectra\Http\Message\Request;
use Effectra\Http\Message\Uri;
use Effectra\Http\Message\Stream;

// Create a request
$uri = new Uri('https://api.example.com/users');
$body = new Stream(json_encode(['name' => 'John Doe']));

$request = new Request('POST', $uri, [], $body);

// Get request information
echo $request->getMethod(); // Output: POST
echo $request->getUri(); // Output: https://api.example.com/users
echo $request->getBody()->getContents(); // Output: {"name":"John Doe"}
```

### Creating a Response

```php
use Effectra\Http\Message\Response;
use Effectra\Http\Message\Stream;

// Create a response
$body = new Stream('Hello, world!');

$response = new Response(200, [], $body);

// Get response information
echo $response->getStatusCode(); // Output: 200
echo $response->getReasonPhrase(); // Output: OK
echo $response->getBody()->getContents(); // Output: Hello, world!
```

### Creating a Stream

```php
use Effectra\Http\Message\Stream;

// Create a stream from a string
$stream = new Stream('Hello, world!');

// Read from the stream
echo $stream->getContents(); // Output: Hello, world!

// Write to the stream
$stream->write('Goodbye!');
echo $stream->getContents(); // Output: Goodbye!
```

### Working with Uploaded Files

```php
use Effectra\Http\Message\UploadedFile;

// Create an uploaded file instance
$file = $_FILES['my_file'];

$uploadedFile = new UploadedFile(
    $file['tmp_name'],
    $file['size'],
    $file['error'],
    $file['name'],
    $file['type']
);

// Get information about the uploaded file
echo $uploadedFile->getClientFilename(); // Output: my_file.txt
echo $uploadedFile->getClientMediaType(); // Output: text/plain

// Move the uploaded file to a target location
$targetPath = '/path/to/destination';
$uploadedFile->moveTo($targetPath);
```

### Working with URIs

```php
use Effectra\Http\Message\Uri;

// Create a URI instance
$uri = new Uri('https://example.com/path?query=param#fragment');

// Get individual URI components
echo $uri->getScheme(); // Output: https
echo $uri->getHost(); // Output: example.com
echo $uri->getPath(); // Output: /path
echo $uri->getQuery(); // Output: query=param
echo $uri->getFragment(); // Output: fragment

// Modify the URI components
$modifiedUri = $uri
    ->withScheme('http')
    ->withHost('example.org')
    ->withPath('/new-path')
    ->withQuery('key=value')
    ->withFragment('new-fragment');

echo $modifiedUri; // Output: http://example.org/new-path?key=value#new-fragment
```

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request on GitHub.

## License

This library is released under the MIT License. See [LICENSE](LICENSE) for more information.