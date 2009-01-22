load('jslint.js');

var body = arguments[0];

if (! JSLINT(body)) {
  print(JSLINT.report());
}
