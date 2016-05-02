/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "group_in_group")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "GroupInGroup.findAll", query = "SELECT g FROM GroupInGroup g"),
    @NamedQuery(name = "GroupInGroup.findByGroupInGroupId", query = "SELECT g FROM GroupInGroup g WHERE g.groupInGroupId = :groupInGroupId")})
public class GroupInGroup implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "group_in_group_id")
    private Long groupInGroupId;
    @JoinColumn(name = "group_src_id", referencedColumnName = "group_id")
    @ManyToOne(optional = false)
    private GroupInfo groupSrcId;
    @JoinColumn(name = "group_tar_id", referencedColumnName = "group_id")
    @ManyToOne(optional = false)
    private GroupInfo groupTarId;

    public GroupInGroup() {
    }

    public GroupInGroup(Long groupInGroupId) {
        this.groupInGroupId = groupInGroupId;
    }

    public Long getGroupInGroupId() {
        return groupInGroupId;
    }

    public void setGroupInGroupId(Long groupInGroupId) {
        this.groupInGroupId = groupInGroupId;
    }

    public GroupInfo getGroupSrcId() {
        return groupSrcId;
    }

    public void setGroupSrcId(GroupInfo groupSrcId) {
        this.groupSrcId = groupSrcId;
    }

    public GroupInfo getGroupTarId() {
        return groupTarId;
    }

    public void setGroupTarId(GroupInfo groupTarId) {
        this.groupTarId = groupTarId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (groupInGroupId != null ? groupInGroupId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof GroupInGroup)) {
            return false;
        }
        GroupInGroup other = (GroupInGroup) object;
        if ((this.groupInGroupId == null && other.groupInGroupId != null) || (this.groupInGroupId != null && !this.groupInGroupId.equals(other.groupInGroupId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.GroupInGroup[ groupInGroupId=" + groupInGroupId + " ]";
    }
    
}
