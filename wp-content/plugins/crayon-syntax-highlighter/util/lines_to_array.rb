#!/usr/bin/env ruby
# Converts lines from a file into an array of strings.
if ARGV.size == 0
	puts "lines_to_array.rb <file>"
else
	lines = File.readlines(ARGV[0])
	lines = lines.map do |line|
		"'" + line.strip + "'"
	end
	puts '[' + lines.join(',') + ']'
end