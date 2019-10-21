#!/bin/bash
# echo command
echo Bash Start


git add .
git reset -- gitbash.sh
git reset -- assets/uploads/qrcode3.png

git status

echo Bash End
#read

# Log Commit Name
#git log --pretty=format:"%h - %an, %ar : %s"

#$PARENT = bfea469
#$PARENT = '05129c7'
#$COMMIT = '1f1e3f9'
#$COMMIT = '9653c8b'

#git diff-tree --no-commit-id --name-only -r $PARENT $COMMIT
#git diff 05129c7 1f1e3f9 --name-only
#git diff $PARENT $COMMIT --name-only

#git show --color --pretty=format:%b $COMMIT