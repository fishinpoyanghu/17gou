
	/**
	 * @lijianyun@ddtkj.com
	 * 2015.07.15
	 * 会员账户金额格式化(显示千分位、后面保留俩位小数)
	 * */
	function moneyFormat (money) {
		if (money == 0) {
			return 0.0;
		} else {
			if(money<0){
				money = money*(-1);
				n = 2;
				money = parseFloat((money + "").replace(/[^\d\.-]/g, ""))
				.toFixed(n)
				+ "";
				var l = money.split(".")[0].split("").reverse(), r = money
				.split(".")[1];
				t = "";
				for (i = 0; i < l.length; i++) {
					t += l[i]
					+ ((i + 1) % 3 == 0 && (i + 1) != l.length ? ","
							: "");
				}
				return "-"+t.split("").reverse().join("") + "." + r;
			}else{
				n = 2;
				money = parseFloat((money + "").replace(/[^\d\.-]/g, ""))
				.toFixed(n)
				+ "";
				var l = money.split(".")[0].split("").reverse(), r = money
				.split(".")[1];
				t = "";
				for (i = 0; i < l.length; i++) {
					t += l[i]
					+ ((i + 1) % 3 == 0 && (i + 1) != l.length ? ","
							: "");
				}
				return t.split("").reverse().join("") + "." + r;
			}
		}
	};
	
	/**
	 * @lijianyun@ddtkj.com
	 * 2015.07.18
	 * 会员账户金额格式化(显示千分位、没有小数)
	 * */
	function moneyIntFormat (money) {
		if (money == 0) {
			return money;
		} else {
			if(money<0){
				money = money*(-1);
				n = 2;
				money = parseFloat((money + "").replace(/[^\d\.-]/g, ""))
						.toFixed(n)
						+ "";
				var l = money.split(".")[0].split("").reverse(), 
				//	r = money.split(".")[1];
				t = "";
				for (i = 0; i < l.length; i++) {
					t += l[i]
							+ ((i + 1) % 3 == 0 && (i + 1) != l.length ? ","
									: "");
				}
				return "-"+t.split("").reverse().join("");
			}else{
				n = 2;
				money = parseFloat((money + "").replace(/[^\d\.-]/g, ""))
						.toFixed(n)
						+ "";
				var l = money.split(".")[0].split("").reverse(), 
				//	r = money.split(".")[1];
				t = "";
				for (i = 0; i < l.length; i++) {
					t += l[i]
							+ ((i + 1) % 3 == 0 && (i + 1) != l.length ? ","
									: "");
				}
				return t.split("").reverse().join("");	
			}
		}
	};