(function ($) {
  ("use strict");

  /**
   * remove success/error message when click cross button
   */
  $(function () {
    $(document).on('click', '.admin-msg', function() {
      $(this).addClass('hide-admin-msg');
      var url = $(location).attr("href");
      url = removeParam('form_submitted', url);
      url = removeParam('admin_notice', url);
      url = removeParam('is_valid_form', url);
      url = removeParam('over_500_success', url);
      url = removeParam('over_500_error', url);
      window.history.pushState('data', 'title', url);
      // console.log(url)
    })
  })

  function removeParam(name, _url){
      var reg = new RegExp("((&)*" + name + "=([^&]*))","g");
      return _url.replace(reg,'');
  }
  /**
   *text editor with lime number.
   *
   */
  $(function () {
    const input = document.querySelector("#editor textarea");

    if (input != null) {
      const gutter = document.querySelector(".gutter");
      let val = input.value;

      let lineBreaks = val.match(/\n/gi) || [];
      numOfLines = lineBreaks.length ? lineBreaks.length + 1 : 1;
      addLines();

      function update(e) {
        val = input.value;

        let lineBreaks = val.match(/\n/gi) || [];
        numOfLines = lineBreaks.length ? lineBreaks.length + 1 : 1;
        addLines();
      }

      function addLines() {
        gutter.innerHTML = "";
        for (var i = 0; i < numOfLines; i++) {
          var el = document.createElement("span");
          el.innerHTML = i + 1;
          gutter.appendChild(el);
        }
      }

      input.addEventListener("input", update);
    }
  });

  /**
   *IAB tags select filter.
   */
  $(function () {
    if (document.getElementById("select-tools") != null) {
      var firtLoad = true;
      var that = this;
      this.selectize = $("#select-tools").selectize({
        preload: true,
        maxItems: null,
        maxOptions: null,
        valueField: "id",
        placeholder: "Start typing",
        labelField: "tax_name",
        searchField: "tax_name",
        load: function (query, callback) {
          $.when(
            $.ajax({
              url:
                "/wp-json/safetag-api/v1/iabtags" +
                "/?search=" +
                encodeURIComponent(query),
              type: "GET",
              headers: {
                "Content-type": "application/json; charset=UTF-8",
                "X-WP-Nonce": safetagSetting.nonce,
              },
              error: function () {
                callback();
              },
              success: function (res) {
                callback(Object.values(res));
              },
            })
          ).then(function () {
            if (firtLoad) {
              var safetag_meta_tags_values = document.getElementById(
                "safetag_meta_tags_values"
              );
              if (safetag_meta_tags_values.value != "") {
                const tags_values = JSON.parse(safetag_meta_tags_values.value);

                that.selectize[0].selectize.setValue(tags_values);
                firtLoad = false;
              }
            }
          });
        },
      });
    }
  });

  /**
   *IAB audience tags select filter.
   */
  $(function () {
    if (document.getElementById("audience-select-tools") != null) {
      var firtLoad = true;
      var that = this;
      this.selectize = $("#audience-select-tools").selectize({
        preload: true,
        maxItems: null,
        maxOptions: null,
        valueField: "id",
        placeholder: "IAB Audience Tags",
        labelField: "tax_name",
        searchField: "tax_name",
        load: function (query, callback) {
          $.when(
            $.ajax({
              url:
                "/wp-json/safetag-api/v1/audiencetags" +
                "/?search=" +
                encodeURIComponent(query),
              type: "GET",
              headers: {
                "Content-type": "application/json; charset=UTF-8",
                "X-WP-Nonce": safetagSetting.nonce,
              },
              error: function () {
                callback();
              },
              success: function (res) {
                callback(Object.values(res));
              },
            })
          ).then(function () {
            if (firtLoad) {
              var safetag_meta_tags_values = document.getElementById(
                "safetag_meta_tags_values"
              );
              if (safetag_meta_tags_values.value != "") {
                const tags_values = JSON.parse(safetag_meta_tags_values.value);

                that.selectize[0].selectize.setValue(tags_values);
                firtLoad = false;
              }
            }
          });
        },
      });
    }
  });

  /**
     * Import the text file
     */
   $(function () {
    var importButton = document.getElementById("import_button");
    var rowObj = null;
    var jsonarray = [];
    var iscsv = true;
    var selectedFile = null;
    var selectedKeyword = null;
    var columnsArray = [];
    var i = 0;
    if (importButton) {
      importButton.addEventListener("click", openDialog);

      var okclicked = document.getElementById("ok");
      okclicked.addEventListener("click", openDialog2);

      const safetagFileElement = document.getElementById("safetag_file_import");

      function openDialog() {
        safetagFileElement.value = null;
        safetagFileElement.click();
      }

      function openDialog2() {
        document.getElementById("popupForm").style.display = "none";
        var keywordChecked = null;
        var excludeHeader = false;
        var e = document.getElementById("keywordsDropdown");
        keywordChecked = e.value;
        if(document.getElementById("include2").checked){
          excludeHeader = true;
        }
        processCSV(keywordChecked, excludeHeader);
      }

      function processCSV(keywordChecked, excludeHeader) {
        if (excludeHeader == false) {
          for (k=0; k<columnsArray.length; k++) {
            jsonarray[k] = columnsArray[k];
            i = k;
          }
          i++;
        }
        Object.keys(rowObj).forEach(function(key){
          var valueExists = jsonarray.includes(rowObj[key][keywordChecked]);
          if (!valueExists) {
            jsonarray[i] = rowObj[key][keywordChecked];
            jsonarray[i] = jsonarray[i].replace(/"/g,"");
            i++;
          }
        })
        var keywordElement = document.getElementById("keywords");
          if (iscsv) {
            if (keywordElement.value) {
              keywordElement.value += "\n" + contents;
            } else {
              keywordElement.value = contents;
            }
          } else {
              jsonarray.forEach(function(item){
                keywordElement.value += "\n" + item;
              })
          }
      }

      function populateHeaders(headers) {
        let length = headers.length;
        select = document.getElementById('keywordsDropdown');
        for(j=0; j<length; j++) {
          var opt = document.createElement('option');
          opt.value = headers[j];
          opt.innerHTML = headers[j];
          select.appendChild(opt);
        }

      }

      safetagFileElement.onchange = () => {
        selectedFile = safetagFileElement.files[0];
        safetagFileTextElement = document.getElementById(
          "safetag-file-name-label"
        );

        console.log("aja nel");

        if (selectedFile) {
          // document.getElementById("popupForm").style.display = "block";
          var fileReader = new FileReader();
          var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx|.csv)$/;
          fileReader.onload = function (e) {
            var contents = e.target.result;
            var jsonObj = '';
            // contents for xls,xlsx
            if (regex.test(safetagFileElement.value.toLowerCase())) {
              iscsv = false;
              var contentstemp = XLSX.read(e.target.result, {
                type: 'binary'
              });
              contentstemp.SheetNames.forEach(function(sheetName){
                columnsArray = XLSX.utils.sheet_to_json(contentstemp.Sheets[sheetName], { header: 1 })[0];
                rowObj =XLSX.utils.sheet_to_row_object_array(contentstemp.Sheets[sheetName]);
                // hideCheckbox();
                populateHeaders(columnsArray);
                document.getElementById("popupForm").style.display = "block";
                // console.log(rowObj[0]['Keyword']);
                // jsonObj += JSON.stringify(rowObj);
                })
              contents = jsonObj;
            }
          };
          if (regex.test(safetagFileElement.value.toLowerCase())) {
            fileReader.readAsBinaryString(selectedFile);
          } else {
            fileReader.readAsText(selectedFile);
          }

          safetagFileTextElement.textContent = selectedFile.name;
        } else {
          safetagFileTextElement.textContent = "";
        }
      };
    }
  });

  $(window).bind("load", function() {
    const timestamp       = `?time=${get_rounded_time() || ''}`;
    const camp_span_class = $(".camp_span_class");

    if(camp_span_class.length > 0) {
      $.ajax({
        url: safetagSetting.ajaxurl + timestamp,
        type: 'get',
        data: {
            'action': 'get_all_camp_key_count'
        },
        error: function(error) {
          console.log(error)
        }
      }).done(function(resp) {
          let camp_list = JSON.parse(resp);

          camp_span_class.map(function() {
            let id      = $(this).attr('id');
            let camp_id = $(this).data('campid');
            if(camp_list[camp_id] !== undefined) {
              $('#'+id).text(camp_list[camp_id])
            } else {
              $('#'+id).text(0)
            }
          });
      });
    }
  });
})(jQuery);

/**
 * Get the current rounded time in milliseconds
 * @param {int} interval
 * @returns string
 */
function get_rounded_time(interval = 5) {
  const currentTime = new Date().getTime();
  // Calculate the nearest interval in minutes
  const fiveMinuteInterval = interval *  60 * 1000; // {interval} minutes in milliseconds

  return Math.floor(currentTime / fiveMinuteInterval) * fiveMinuteInterval;
}
