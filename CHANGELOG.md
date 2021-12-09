# Changelog

All notable changes to `laravel-payfast` will be documented in this file.

## 0.1.0 - 2021-12-07

- more refactoring to keep payfast class small and compact
- first attempt at getting onsite payments work - card locked out with 'Unfortunately this card does not support recurring payments.'
- the order is now a numeric and a dash with today's date and time
- improvement on invalid morph model exception message

## 0.0.3 - 2021-12-07

- refactored webhooks
- all tests passing for the first time

## 0.0.2 - 2021-12-05

- ability to create a new ad-hoc subscription with any amount
- ability to cancel a subscription
- copied many of the Laravel Paddle tests and made them work
- foundation jetstream block to show subscription information
- ability to create a new ad-hoc subscription with R 0 amount
- pay button generation

## 0.0.1 - 2021-12-03

- initial release
