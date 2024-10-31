// Please do not remove this file. We use this file for wp_localize_script.

//for Outgoing GAM call
! function(o, g) {
  g.googletag = g.googletag || {};
  g.googletag.cmd = g.googletag.cmd || [];
  g.googletag.cmd.push(function() {
    const safetagList = window.safetag_lists || {};
    const safetagIncludeList = safetagList.Include || [];
    const safetagExcludeList = safetagList.Exclude || [];

    if (Array.isArray(safetagExcludeList) && safetagExcludeList.length) {
      const safetagExclude = safetagExcludeList.map(val => val.trim());
      const excludeList = safetagExclude.join(',');
      g.googletag.pubads().setTargeting('safetag_exclude_list', excludeList);
    }
    if (Array.isArray(safetagIncludeList) && safetagIncludeList.length) {
      const safetagInclude = safetagIncludeList.map(val => val.trim());
      const includeList = safetagInclude.join(',');
      g.googletag.pubads().setTargeting('safetag_include_list', includeList);
    }

    if (typeof window.safetag_fpd === 'undefined')  return;

    const fpdData = window.safetag_fpd;
    if (typeof fpdData.cattax !== 'undefined') {
      g.googletag.pubads().setTargeting('safetag_fpd_cattax', fpdData.cattax);
    }

    if (typeof fpdData.iab === 'undefined')  return;

    const fpdIab = Object.entries(fpdData.iab);
    fpdIab.forEach(fpdKey => {
      const fpdIabCat = Object.entries(fpdKey[1]);
      try {
        fpdIabCat.forEach(fpdValue => {
          if (typeof fpdValue[0] !== 'undefined' && typeof fpdValue[1] !== 'undefined' && fpdValue[0] !== null && fpdValue[0] !== 'null' && fpdValue[1] !== null && fpdValue[1] !== 'null') {
            const keyname = `safetag_fpd_iab_${fpdKey[0]}_${fpdValue[0]}`;
            let fpdVal = '';
            if (Array.isArray(fpdValue[1])) {
              fpdVal = fpdValue[1].join();
            } else if (typeof fpdValue[1] !== 'object') {
              fpdVal = fpdValue[1];
            }
            g.googletag.pubads().setTargeting(keyname, fpdVal);
          }
        })
      } catch (e) {
        console.log(e.message)
      }
    })
  })
}(document, window);
