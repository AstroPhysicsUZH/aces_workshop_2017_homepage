#!/bin/sh

sqlite3 registration.sqlite3 ".databases"
chmod o+w registration.sqlite3
