# Covid crawler

Just a simple API scrapper which requests data from a site to obtain daily numbers of COVID cases in Catalunya.

___

## Origin
These requests are calling the same ones pulled from http://aquas.gencat.cat/ca/actualitat/ultimes-dades-coronavirus/ in the "Mapa interactiu per Municipis" section.

## Setup
1. Create a database.
2. Upload script and db.config file to hosting.
3. Configure cron job for daily run of the script.

## How I use it
This is a php script meant to be run by a cron job every day. The script throws close to 1000 requests in a span of almost 2 hours, 1 request every 6 seconds. At the moment there is no logging in case of server error response.