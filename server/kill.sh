/bin/bash
ps aux | grep $1 | grep -v ii | cut -c 9-15 | xargs kill -9
