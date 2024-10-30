var TKLP = TKLP || {};

TKLP.tabs = function () {
	'use strict';

	var tabMenus,
		tabContents;

	tabMenus = document.querySelectorAll('.tk-lp-tabs-form-item');
	tabContents = document.querySelectorAll('.tk-lp-tabs-form-content');

	for (var i = 0; i < tabMenus.length; i++) {
		tabMenus[i].addEventListener('click', function (e) {
			e.preventDefault();

			for (var i = 0; i < tabMenus.length; i++) {
				tabMenus[i].classList.remove('active');
			}

			this.classList.add('active');


			for (var i = 0; i < tabContents.length; i++) {
				tabContents[i].classList.remove('active');
			}

			document.getElementById(this.dataset.id).classList.add('active');

		});
	}
};


TKLP.modals = function () {
	var _targettedModal,
		_triggers = document.querySelectorAll('[data-modal-trigger]'),
		_dismiss = document.querySelectorAll('[data-modal-dismiss]'),
		_body = document.querySelector('body'),
		modalActiveClass = "is-modal-active";

	function showModal(el) {
		_targettedModal = document.querySelector('[data-modal-name="' + el + '"]');
		_targettedModal.classList.add(modalActiveClass);
		var children = _targettedModal.querySelector('.tk-lp-first-input');
		if(children.length !== 0){
			children.focus();
		}
		_body.classList.add("popup-active");
	}

	function hideModal(event) {
		if (event === undefined || event.target.hasAttribute('data-modal-dismiss')) {
			_targettedModal.classList.remove(modalActiveClass);
			_body.classList.remove("popup-active");
		}
	}

	function bindEvents(el, callback) {
		for (i = 0; i < el.length; i++) {
			(function (i) {
				el[i].addEventListener('click', function (event) {
					event.preventDefault();
					callback(this, event);
				});
			})(i);
		}
	}

	function triggerModal() {
		bindEvents(_triggers, function (that) {
			showModal(that.dataset.modalTrigger);
		});
	}

	function dismissModal() {
		bindEvents(_dismiss, function (that) {
			hideModal(event);
		});
	}

	function initModal() {
		triggerModal();
		dismissModal();
	}

	document.onkeydown = function(evt) {
		evt = evt || window.event;
		if (evt.keyCode === 27) {
			hideModal();
		}
	};

	document.addEventListener('click', function (event) {
		var _this = event.target;
		var _clickInForm = _this.closest(".tk-lp-form");
		var _clickInModal = _this.closest(".is-modal-active");

		if ( !_clickInForm && _clickInModal ) {
			hideModal();
		}
	});

	initModal();
};


// Fix the dropdown on the right side
TKLP.fixDropdownPosition = function () {

	var dropdown = document.querySelector('.tk-lp-user-menu-dropdown');
	if(dropdown != null){
		var menu_width = window.innerWidth;
		var dropdownPosition = dropdown.getBoundingClientRect();

		if (dropdown.offsetWidth + dropdownPosition.left > menu_width) {
			dropdown.classList.add("dropdown-right");
		} else {
			if (menu_width == dropdown.offsetWidth || (menu_width - dropdown.offsetWidth < 20)) {
				dropdown.classList.add("dropdown-right");
			}
			if (dropdownPosition.left + dropdown.offsetWidth < menu_width) {
				dropdown.classList.add("dropdown-left");
			}
		}
	}
};

document.addEventListener("DOMContentLoaded", function (event) {
	TKLP.tabs();
	TKLP.modals();
	TKLP.fixDropdownPosition();
});