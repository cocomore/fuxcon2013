---
title: Behavior Specification
name: behavior
layout: implementation
permalink: /behavior/
---
I use a suite of behavioral tests, written in the [Gerkin language](https://github.com/cucumber/cucumber/wiki) for the [Behat tool](http://behat.org/), to verify consistant behavior across the implementation of a simple portfolio site in these four frameworks.

These are the features specified:

<table class="table table-bordered table-striped">
  <thead>
    <tr><th style="white-space: nowrap">Feature</th><th>Scenarios</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>Startpage</td>
      <td>
        <ul>
          <li>List projects in columns</li>
          <li>Pagination</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="white-space: nowrap">Project details</td>
      <td>
        <ul>
          <li>Linked from index page</li>
          <li>Page contains title, scaled picture, and dates (tagging not tested yet)</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="white-space: nowrap">Project creation</td>
      <td>
        <ul>
          <li>Only logged-in users may create projects</li>
          <li>Project is created when a form with title, picture, description, dates, and tags is filled in and submitted</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="white-space: nowrap">Project editing</td>
      <td>
        <ul>
          <li>Owners and admins may edit projects</li>
          <li>Changes are committed when filling in and submitting an edit form</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="white-space: nowrap">User accounts</td>
      <td>
        <ul>
          <li>User registration</li>
          <li>User login</li>
          <li>User logout</li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>

You can find the behaviour spec in [this repository](https://github.com/cocomore/fuxcon2013) in the [features](https://github.com/cocomore/fuxcon2013/tree/master/features) folder.
