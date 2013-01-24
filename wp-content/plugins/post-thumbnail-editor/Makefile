
JSOUTPUT_DEV = js/pte.full.dev.js
JSOUTPUT_MIN = js/pte.full.js
COFFEE       = coffee
COFFEE_FLAGS = -s -p
COFFEE_FILES = js/header.coffee \
					js/log.coffee \
					js/pte_admin.coffee \
					js/pte.coffee
JS_FILES     = apps/jquery-tmpl/jquery.tmpl.js
TMPFILE     := $(shell mktemp)


CSSOUTPUT_DEV = css/pte.dev.css
CSSOUTPUT_MIN = css/pte.css
SCSSFILE      = css/pte.scss
SASS          = sass
CSSFILES      = css/reset.css

# create/overwrite the JSMINIFIER command for local
# local.mk is not tracked in git project
include $(wildcard local.mk)

# The GOOGLE macro is defined in local.mk to point to compiler.jar
ifdef GOOGLE
JSMINIFIER   = java -jar "$(GOOGLE)" --js $(JSOUTPUT_DEV) --js_output_file $(JSOUTPUT_MIN)
else
JSMINIFIER   = cp $(JSOUTPUT_DEV) $(JSOUTPUT_MIN)
endif

# The YUI macro is defined in local.mk to point to yuicompressor.jar
ifdef YUI
CSSMINIFIER   = java -jar "$(YUI)" --type css -o $(CSSOUTPUT_MIN) $(CSSOUTPUT_DEV)
else
CSSMINIFIER   = cp $(CSSOUTPUT_DEV) $(CSSOUTPUT_MIN)
endif


# A simple make will compile the js/css and minify them
all: minify-js minify-css trans

# Build javascript
$(JSOUTPUT_MIN): $(JSOUTPUT_DEV)
	@echo "Minifying javascript"
	$(JSMINIFIER)

$(JSOUTPUT_DEV): $(COFFEE_FILES) $(JS_FILES)
	@echo "Building javascript"
	cat $(JS_FILES) > $(JSOUTPUT_DEV)
	cat $(COFFEE_FILES) > $(TMPFILE)
	$(COFFEE) $(COFFEE_FLAGS) < $(TMPFILE) >> $(JSOUTPUT_DEV)


# BUILD CSS
$(CSSOUTPUT_DEV): $(SCSSFILE) $(CSSFILES)
	@echo "Building CSS"
	cat $(CSSFILES) > $(CSSOUTPUT_DEV)
	$(SASS) $(SCSSFILE) >> $(CSSOUTPUT_DEV)

$(CSSOUTPUT_MIN): $(CSSOUTPUT_DEV)
	@echo "Minifying CSS"
	$(CSSMINIFIER)

# Shortcuts
js: $(JSOUTPUT_DEV)
minify-js: $(JSOUTPUT_MIN)
css: $(CSSOUTPUT_DEV)
minify-css: $(CSSOUTPUT_MIN)

#  i18n - Defined in local.mk to point to wordpress makepot.php script
trans:
	@echo "Creating Internationalization Template"
ifdef I18N
	cd i18n; \
	php '$(I18N)' wp-plugin ../
endif

# Clean
OUTPUTFILES = $(wildcard $(CSSOUTPUT_MIN) $(CSSOUTPUT_DEV) $(JSOUTPUT_MIN) $(JSOUTPUT_DEV))
clean:
	@echo "Cleaning up"
	$(if $(OUTPUTFILES), rm $(OUTPUTFILES))

# vi: ts=3
