#!/bin/bash
#参数
start_with=3 #路径名截取
str1='<source-file src="src/android/libs/'
str2='" target-dir="res/libs"/>'

#执行
find . | awk -v str1="$str1" -v str2="$str2" -v start_with=$start_with  '{print str1 substr($1,start_with) str2}'
