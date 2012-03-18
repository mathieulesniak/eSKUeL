#!/bin/bash

base=`dirname "$0"`/../libs/
output="autoload.php"

cd $base

grep -rE '^\s*((abstract +)?class|interface) ' ./ \
| perl -pe 's!(.*):\s*(?:(?:abstract\s+)?class|interface) (\S*).*!'\''$2'\'' => '\''$1'\'',!' \
> /tmp/make_class_$$
echo "<?php" > "$output"
echo "return array(" >> "$output"
cat /tmp/make_class_$$ | sort >>  "$output"
echo ");" >>  "$output"
echo -n "?>" >>  "$output"

echo -e "`pwd`/$output successfuly generated"