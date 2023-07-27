<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Damage extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->cbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Damage Entry";
        $data['damageId'] = 0;
        $data['invoice'] = $this->mt->generateDamageInvoice();
        $data['content'] = $this->load->view('Administrator/damage/damageEntry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function damageEdit($damageId)
    {
        $data['title']    = "Damage Update";
        $damage           = $this->db->query("select * from tbl_purchasedamage where id = ?", $damageId)->row();
        $data['damageId'] = $damageId;
        $data['invoice']  = $damage->Damage_invoiceNo;
        $data['content']  = $this->load->view('Administrator/damage/damageEntry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $invoice = $data->damage->invoiceNo;
            $invoiceCount = $this->db->query("select * from tbl_purchasedamage where Damage_invoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generateDamageInvoice();
            }



            $damage = array(
                'Damage_invoiceNo' => $invoice,
                'supplierID'       => $data->damage->supplierId,
                'Damage_Date'      => $data->damage->damageDate,
                'Damage_Total'     => $data->damage->Total,
                'note'             => $data->damage->note,
                'Status'           => 'a',
                "AddBy"            => $this->session->userdata("FullName"),
                'Branch_Id'        => $this->session->userdata("BRANCHid")
            );

            $this->db->insert('tbl_purchasedamage', $damage);

            $damageId = $this->db->insert_id();

            foreach ($data->cart as $cartProduct) {
                $damageDetails = array(
                    'Damage_id'          => $damageId,
                    'Damage_ProductId'   => $cartProduct->productId,
                    'Damage_Quantity'    => $cartProduct->quantity,
                    'Damage_Price'       => $cartProduct->purchaseRate,
                    'Damage_TotalAmount' => $cartProduct->total,
                    'Status'             => 'a',
                    'Damage_BranchId'    => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasedamage_detail', $damageDetails);

                //update stock
                $this->db->query("
                    update tbl_currentinventory 
                    set purchasedamage_quantity = purchasedamage_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$cartProduct->quantity, $cartProduct->productId, $this->session->userdata('BRANCHid')]);
            }
            $res = ['success' => true, 'message' => 'Damage save success', 'damageId' => $damageId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    function damageRecord()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Damage Record";
        $data['content'] = $this->load->view('Administrator/damage/damageRecord', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function getDamages()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pmd.Damage_Date between '$data->dateFrom' and '$data->dateTo'";
        }

        // if(isset($data->userFullName) && $data->userFullName != ''){
        //     $clauses .= " and pmd.AddBy = '$data->userFullName'";
        // }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pmd.supplierId = '$data->supplierId'";
        }

        if (isset($data->damageId) && $data->damageId != 0 && $data->damageId != '') {
            $clauses .= " and pmd.id = '$data->damageId'";
            $damageDetails = $this->db->query("
                select 
                    pmdd.*,
                    p.Product_Code,
                    p.Product_Name,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_purchasedamage_detail pmdd
                join tbl_product p on p.Product_SlNo = pmdd.Damage_ProductId
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where pmdd.Damage_id = ?
            ", $data->damageId)->result();

            $res['damageDetails'] = $damageDetails;
        }
        $damages = $this->db->query("
            select
            concat(pmd.Damage_invoiceNo, ' - ', s.Supplier_Name) as invoice_text,
            pmd.*,
            s.Supplier_Name,
            s.Supplier_Mobile,
            s.Supplier_Email,
            s.Supplier_Code,
            s.Supplier_Address,
            s.Supplier_Type
            from tbl_purchasedamage pmd
            join tbl_supplier s on s.Supplier_SlNo = pmd.supplierId
            where pmd.Branch_Id = '$branchId' 
            and pmd.Status = 'a'
            $clauses
            order by pmd.id desc
        ")->result();

        $res['damages'] = $damages;

        echo json_encode($res);
    }


    public function purchasedamageInvoicePrint($damageId)
    {
        $data['title'] = "Purchase Damage Invoice";
        $data['damageId'] = $damageId;
        $data['content'] = $this->load->view('Administrator/damage/purchasedamageReport', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function updateDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $oldPurchaseDetails = $this->db->query("select * from tbl_purchasedamage_detail where Damage_id = ?", $data->damage->damageId)->result();

            foreach ($oldPurchaseDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory 
                    set purchasedamage_quantity = purchasedamage_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->Damage_Quantity, $product->Damage_ProductId, $this->session->userdata('BRANCHid')]);
            }

            $this->db->query("delete from tbl_purchasedamage_detail where Damage_id = ?", $data->damage->damageId);


            $damage = array(
                'Damage_invoiceNo' => $data->damage->invoiceNo,
                'supplierID'       => $data->damage->supplierId,
                'Damage_Date'      => $data->damage->damageDate,
                'Damage_Total'     => $data->damage->Total,
                'note'             => $data->damage->note,
                'Status'           => 'a',
                "AddBy"            => $this->session->userdata("FullName"),
                'Branch_Id'        => $this->session->userdata("BRANCHid")
            );

            $this->db->where('id', $data->damage->damageId);
            $this->db->update('tbl_purchasedamage', $damage);

            foreach ($data->cart as $cartProduct) {
                $damageDetails = array(
                    'Damage_id'          => $data->damage->damageId,
                    'Damage_ProductId'   => $cartProduct->productId,
                    'Damage_Quantity'    => $cartProduct->quantity,
                    'Damage_Price'       => $cartProduct->purchaseRate,
                    'Damage_TotalAmount' => $cartProduct->total,
                    'Status'             => 'a',
                    'Damage_BranchId'    => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasedamage_detail', $damageDetails);

                //update stock
                $this->db->query("
                    update tbl_currentinventory 
                    set purchasedamage_quantity = purchasedamage_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$cartProduct->quantity, $cartProduct->productId, $this->session->userdata('BRANCHid')]);
            }
            $res = ['success' => true, 'message' => 'Damage update successfully', 'damageId' => $data->damage->damageId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
}
