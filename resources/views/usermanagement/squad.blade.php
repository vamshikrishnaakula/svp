@include('layouts.header')
@extends('layouts.footer')
<section id="createsquad" class="content-wrapper_sub tab-content">
            <div class="user_manage">
            <div class="row">
                <div class="col-md-6">
                  <h4>Create Squad</h4>
                </div>
          </div>
          <form>
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="row">
                    <label for="squadno" class="col-md-4">Squad Number</label>
                    <input type="text" class="form-control col-md-4" id="squadno">
                </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                            <label class="col-md-4">Drill Inspector</label>
                            <select class="form-control col-md-8" id="sel1">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                              </select>
                    </div>
                </div>
            </div>
          </form>
          <div class="row mt-4">
              <div class="col-md-6">
                <div class="probationerlist">
                    <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Probationers List</h6>
                        </div>
                        <div class="col-md-7">
                            <input type="search" class="form-control" placeholder="search"/>
                        </div>
                    </div>
                </div>
                    <table>
                        <thead>
                                <tr>
                                    <th width="10%"></th>
                                    <th>Batch No</th>
                                    <th>Roll No</th>
                                    <th>Name</th>
                                    <th>Grades</th>
                                </tr>
                        </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" class="form-control" /></td>
                                    <td>efwef</td>
                                    <td>fwefw</td>
                                    <td>fwefwe</td>
                                    <td>fwefwwe</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="form-control" /></td>
                                    <td>efwef</td>
                                    <td>fwefw</td>
                                    <td>fwefwe</td>
                                    <td>fwefwwe</td>
                                </tr>
                            </tbody>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn formBtn addBtn">ADD</button>
                </div>

              </div>
              <div class="col-md-6">
                <div class="probationerlist">
                    <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Added Members</h6>
                        </div>
                        <div class="col-md-7">
                            <input type="search" class="form-control" placeholder="search" />
                        </div>
                    </div>
                </div>
                    <table>
                        <thead>
                                <tr>
                                    <th width="10%"></th>
                                    <th>Batch No</th>
                                    <th>Roll No</th>
                                    <th>Name</th>
                                    <th>Grades</th>
                                </tr>
                        </thead>
                        <tbody>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" class="form-control" /></td>
                                    <td>gwgwgr</td>
                                    <td>bbrtbrt</td>
                                    <td>erreg</td>
                                    <td>btbrtbr</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="form-control" /></td>
                                    <td>gwgwgr</td>
                                    <td>bbrtbrt</td>
                                    <td>erreg</td>
                                    <td>btbrtbr</td>
                                </tr>
                            </tbody>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn formBtn removeBtn">Remove</button>
                </div>
              </div>
          </div>

        </div>
        <div class="usersubmitBtns mt-5">
            <div class="mr-4">
                <button type="submit" class="btn formBtn submitBtn">Submit</button>
            </div>
            <div>
                <button type="button" class="btn formBtn cancelBtn">Cancel</button>
            </div>
        </div>
        </section>
